<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Article>
 *
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }


    public function search($searchTerm, $sortField = 'nom', $sortOrder = 'asc', $currentPage = 1, $perPage = 2)
    {
        $qb = $this->createQueryBuilder('u');

        if ($searchTerm) {
            $qb->where('u.nom LIKE :searchTerm OR u.prix LIKE :searchTerm OR u.description LIKE :searchTerm OR u.quantite  LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        // Ensure the sortField is one of the valid fields
        if (!in_array($sortField, ['nom', 'prix', 'description'])) {
            $sortField = 'nom'; // Default field to sort by
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




    //    /**
    //     * @return Article[] Returns an array of Article objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Article
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
