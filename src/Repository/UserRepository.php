<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<User>
* @implements PasswordUpgraderInterface<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
        public function search($searchTerm, $sortField = 'firstname', $sortOrder = 'asc', $currentPage = 1, $perPage = 2)
        {
            $qb = $this->createQueryBuilder('u');
        
            if ($searchTerm) {
                $qb->where('u.firstname LIKE :searchTerm OR u.lastname LIKE :searchTerm OR u.email LIKE :searchTerm')
                   ->setParameter('searchTerm', '%' . $searchTerm . '%');
            }
        
            // Ensure the sortField is one of the valid fields
            if (!in_array($sortField, ['firstname', 'lastname', 'email'])) {
                $sortField = 'firstname'; // Default field to sort by
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
        
        public function countByRole(string $role): int
        {
            return $this->createQueryBuilder('u')
                ->select('count(u.id)')
                ->where('u.roles LIKE :roles')
                ->setParameter('roles', '%"'.$role.'"%') // Make sure the role is wrapped in quotes inside the LIKE statement
                ->getQuery()
                ->getSingleScalarResult();
        }
        /**
         * Count users by month and role.
         *
         * @return array
         */
        public function countUsersByMonthAndRole(): array
        {
            $conn = $this->getEntityManager()->getConnection();

            $query = $conn->createQueryBuilder()
                ->select('COUNT(*) as user_count', 'MONTH(u.createdAt) as month', 'u.roles')
                ->from('user', 'u')
                ->where('u.createdAt >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)')
                ->groupBy('month', 'u.roles')
                ->orderBy('month', 'ASC')
                ->execute();

            return $query->fetchAllAssociative();
        }
}
