<?php

namespace App\Repository;

use App\Entity\Commentaire;
use App\Entity\Publication;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Commentaire>
 *
 * @method Commentaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commentaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commentaire[]    findAll()
 * @method Commentaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commentaire::class);
    }

//    /**
//     * @return Commentaire[] Returns an array of Commentaire objects
//     */
public function findAllUnderPublication(Publication $publication): array
{
    return $this->createQueryBuilder('c')
    ->join('c.Publication','p')
    ->andWhere('p.id = :val')
    ->setParameter('val', $publication->getId())
    ->orderBy('c.dateC', 'DESC')
    ->getQuery()
    ->getResult();
}
public function search($searchTerm, $sortField = 'dateM', $sortOrder = 'desc',$idp)
    {
        $qb = $this->createQueryBuilder('c')
        ->join('c.Publication','p')
        ->andWhere('p.id = :val')
        ->setParameter('val', $idp);
        if ($searchTerm) {
            $qb
                ->andwhere('c.contenu LIKE :searchTerm ')
                ->setParameter('searchTerm', $searchTerm . '%');
        }

        // Ensure the sortField is one of the valid fields
        if (!in_array($sortField, ['contenu','valide','dateC','dateM','User'])) {
            $sortField = 'dateM'; // Default field to sort by
        }

        // Ensure the sortOrder is either 'asc' or 'desc'
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc'; // Default sort order
        }
         else 
         {
            // Sort by other fields
            $qb->orderBy('c.' . $sortField, $sortOrder);
        }

        return $qb->getQuery()->getResult();
    }
public function findAllValidatedUnderPublication(Publication $publication): array
{
    return $this->createQueryBuilder('c')
    ->join('c.Publication','p')
    ->andWhere('p.id = :val')
    ->setParameter('val', $publication->getId())
    ->andWhere('c.valide = :valide') // Filter by the valide status
    ->setParameter('valide', true) // Assuming 'true' means validated
    ->orderBy('c.dateC', 'DESC')
    ->getQuery()
    ->getResult();
}
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

//    public function findOneBySomeField($value): ?Commentaire
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
