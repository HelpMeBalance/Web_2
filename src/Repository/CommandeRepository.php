<?php

namespace App\Repository;

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;


/**
 * @extends ServiceEntityRepository<Commande>
 *
 * @method Commande|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commande|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commande[]    findAll()
 * @method Commande[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }
    public function countAll(): int
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
    // Custom method to sum total amount of all commandes
    public function sumTotalAmount(): float
    {
        return $this->createQueryBuilder('c')
            ->select('SUM(c.amount)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    // Custom method to retrieve statistics about payment methods
    public function getPaymentMethodStatistics(): array
    {
        return $this->createQueryBuilder('c')
            ->select('c.paymentMethod, COUNT(c.id) as count')
            ->groupBy('c.paymentMethod')
            ->getQuery()
            ->getResult();
    }

    // Add more custom methods as needed...


//    /**
//     * @return Commande[] Returns an array of Commande objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Commande
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
public function search($searchTerm, $sortField = 'id', $sortOrder = 'asc', $currentPage = 1, $perPage = 10)
{
    $qb = $this->createQueryBuilder('c');

    if ($searchTerm) {
        $qb->andWhere('c.id LIKE :searchTerm')
            ->setParameter('searchTerm', '%' . $searchTerm . '%');
    }

    // Ensure the sortField is one of the valid fields
    if (!in_array($sortField, ['id', 'username', 'address', 'paymentMethod', 'status', 'totalPrice'])) {
        $sortField = 'id'; // Default field to sort by
    }

    // Ensure the sortOrder is either 'asc' or 'desc'
    if (!in_array($sortOrder, ['asc', 'desc'])) {
        $sortOrder = 'asc'; // Default sort order
    }

    $qb->orderBy('c.' . $sortField, $sortOrder);

    $query = $qb->getQuery()
        ->setFirstResult(($currentPage - 1) * $perPage)
        ->setMaxResults($perPage);

    return new Paginator($query, true);
}
}
