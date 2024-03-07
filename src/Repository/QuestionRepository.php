<?php

namespace App\Repository;

use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Question>
 *
 * @method Question|null find($id, $lockMode = null, $lockVersion = null)
 * @method Question|null findOneBy(array $criteria, array $orderBy = null)
 * @method Question[]    findAll()
 * @method Question[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Question::class);
    }

//    /**
//     * @return Question[] Returns an array of Question objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('q')
//            ->andWhere('q.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('q.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Question
//    {
//        return $this->createQueryBuilder('q')
//            ->andWhere('q.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
        public function search($searchTerm, $sortField = 'question', $sortOrder = 'asc', $currentPage = 1, $perPage = 2)
        {
            $qb = $this->createQueryBuilder('u');

            if ($searchTerm) {
                $qb->where('u.question LIKE :searchTerm ')
                   ->setParameter('searchTerm', '%' . $searchTerm . '%');
            }

            // Ensure the sortField is one of the valid fields
            if (!in_array($sortField, ['question'])) {
                $sortField = 'question'; // Default field to sort by
            }

            // Ensure the sortOrder is either 'asc' or 'desc'
            if (!in_array($sortOrder, ['asc', 'desc'])) {
                $sortOrder = 'asc'; // Default sort order
            }

            $qb->orderBy('u.' . $sortField, $sortOrder);

            $query = $qb->getQuery()
            ->setFirstResult(($currentPage - 1) * $perPage)
            ->setMaxResults($perPage);
    
        return new Paginator($query, true);
        }
        


}
