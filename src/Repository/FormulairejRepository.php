<?php

namespace App\Repository;

use App\Entity\Formulairej;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Formulairej>
 *
 * @method Formulairej|null find($id, $lockMode = null, $lockVersion = null)
 * @method Formulairej|null findOneBy(array $criteria, array $orderBy = null)
 * @method Formulairej[]    findAll()
 * @method Formulairej[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormulairejRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Formulairej::class);
    }

//    /**
//     * @return Formulairej[] Returns an array of Formulairej objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Formulairej
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
