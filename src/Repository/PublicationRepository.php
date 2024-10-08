<?php

namespace App\Repository;

use App\Entity\Publication;
use App\Entity\SousCategorie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Publication>
 *
 * @method Publication|null find($id, $lockMode = null, $lockVersion = null)
 * @method Publication|null findOneBy(array $criteria, array $orderBy = null)
 * @method Publication[]    findAll()
 * @method Publication[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PublicationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Publication::class);
    }
    public function findPaginatedbycat(int $page, int $perPage, int $cat)
    {
        $query = $this->createQueryBuilder('c')
            ->join('c.Categorie', 'p')
            ->andWhere('p.id = :val')
            ->setParameter('val', $cat)
            ->andWhere('c.valide = :valide') // Filter by the valide status
            ->setParameter('valide', true) // Assuming 'true' means validated
            ->orderBy('c.vues', 'DESC')
            ->addOrderBy('c.dateC', 'DESC')
            ->getQuery();

        return $this->paginate($query, $perPage, $page);
    }
    public function findbycat(int $cat)
    {
        return $this->createQueryBuilder('c')
            ->join('c.Categorie', 'p')
            ->andWhere('p.id = :val')
            ->setParameter('val', $cat)
            ->andWhere('c.valide = :valide') // Filter by the valide status
            ->setParameter('valide', true) // Assuming 'true' means validated
            ->orderBy('c.vues', 'DESC')
            ->addOrderBy('c.dateC', 'DESC')
            ->getQuery()
            ->getResult();
    }
    public function findPaginatedbysouscat(int $page, int $perPage, int $souscat)
    {
        $query = $this->createQueryBuilder('c')
            ->join('c.SousCategorie', 'p')
            ->andWhere('p.id = :val')
            ->setParameter('val', $souscat)
            ->andWhere('c.valide = :valide') // Filter by the valide status
            ->setParameter('valide', true) // Assuming 'true' means validated
            ->orderBy('c.vues', 'DESC')
            ->addOrderBy('c.dateC', 'DESC')
            ->getQuery();

        return $this->paginate($query, $perPage, $page);
    }
    public function findAllsorted()
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.vues', 'DESC')
            ->addOrderBy('c.dateC', 'DESC')
            ->getQuery()
            ->getResult();
    }
    public function     findAllsorteddate()
    {
        return $this->createQueryBuilder('c')
            ->OrderBy('c.dateC', 'DESC')
            ->getQuery()
            ->getResult();
    }
    public function findAllsortedValide()
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.valide = :valide') // Filter by the valide status
            ->setParameter('valide', true) // Assuming 'true' means validated
            ->orderBy('c.vues', 'DESC')
            ->addOrderBy('c.dateC', 'DESC')
            ->getQuery()
            ->getResult();
    }
    public function findbysouscat(int $souscat)
    {
        return $this->createQueryBuilder('c')
            ->join('c.SousCategorie', 'p')
            ->andWhere('p.id = :val')
            ->setParameter('val', $souscat)
            ->andWhere('c.valide = :valide') // Add this line to filter by valide status
            ->setParameter('valide', true) // Assuming 'true' indicates a validated item
            ->orderBy('c.vues', 'DESC')
            ->addOrderBy('c.dateC', 'DESC')
            ->getQuery()
            ->getResult();
    }
    public function findPaginated(int $page, int $perPage)
    {
        $query = $this->createQueryBuilder('p')
            ->Where('p.valide = :valide') // Add this line to filter by valide status of the publication
            ->setParameter('valide', true) // Set true for the valide condition
            ->orderBy('p.vues', 'DESC')
            ->addOrderBy('p.dateC', 'DESC')
            ->getQuery();

        return $this->paginate($query, $perPage, $page);
    }
    private function paginate($query, $perPage, $page)
    {
        $offset = ($page - 1) * $perPage;
        $query->setFirstResult($offset)
            ->setMaxResults($perPage);

        return new Paginator($query, $fetchJoinCollection = true);
    }
    public function search($searchTerm, $sortField = 'dateM', $sortOrder = 'desc', $currentPage = 1, $perPage = 5)
    {
        $qb = $this->createQueryBuilder('p');
        if ($searchTerm) {
            $qb
                ->where('p.contenu LIKE :searchTerm OR p.vues LIKE :searchTerm OR p.titre LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        // Ensure the sortField is one of the valid fields
        if (!in_array($sortField, ['titre', 'contenu','valide','vues','dateC','dateM','comments'])) {
            $sortField = 'dateM'; // Default field to sort by
        }

        // Ensure the sortOrder is either 'asc' or 'desc'
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc'; // Default sort order
        }
        if ($sortField === 'comments') {
            $qb->leftJoin('p.commentaires', 'c')
                ->addGroupBy('p.id') // Group by publication to count comments per publication
                ->addSelect('COUNT(c) AS HIDDEN comment_count') // Select the count of comments
                ->orderBy('p.comOuvert', 'DESC') // Order by comOuvert field in descending order
                ->addOrderBy('comment_count', $sortOrder); // Then order by the count of comments
        } else {
            // Sort by other fields
            $qb->orderBy('p.' . $sortField, $sortOrder);
        }

        $query =  $qb->getQuery()
            ->setFirstResult(($currentPage - 1) * $perPage)
            ->setMaxResults($perPage);
        return new Paginator($query, true);
    }

    //    /**
    //     * @return Publication[] Returns an array of Publication objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Publication
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
