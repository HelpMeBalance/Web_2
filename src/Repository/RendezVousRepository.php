<?php

namespace App\Repository;

use App\Entity\RendezVous;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RendezVous>
 *
 * @method RendezVous|null find($id, $lockMode = null, $lockVersion = null)
 * @method RendezVous|null findOneBy(array $criteria, array $orderBy = null)
 * @method RendezVous[]    findAll()
 * @method RendezVous[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RendezVousRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RendezVous::class);
    }

    public function search($searchTerm, $sortField = 'patient', $sortOrder = 'asc', $currentPage = 1, $perPage = 2)
    {
        $qb = $this->createQueryBuilder('u');

        if ($searchTerm) {
            $qb->innerJoin('u.patient', 'p')
                ->innerJoin('u.user', 'psy')
                ->where('p.firstname LIKE :searchTerm OR psy.firstname LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        // Ensure the sortField is one of the valid fields
        if (!in_array($sortField, ['patient', 'user'])) {
            $sortField = 'patient'; // Default field to sort by
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
//     * @return RendezVous[] Returns an array of RendezVous objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

    //    public function findOneBySomeField($value): ?RendezVous
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
