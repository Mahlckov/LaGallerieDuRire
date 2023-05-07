<?php

namespace App\Repository;

use App\Entity\Humouriste;
use App\Entity\Participant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<Humouriste>
 *
 * @method Humouriste|null find($id, $lockMode = null, $lockVersion = null)
 * @method Humouriste|null findOneBy(array $criteria, array $orderBy = null)
 * @method Humouriste[]    findAll()
 * @method Humouriste[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HumouristeRepository extends ServiceEntityRepository implements PasswordUpgraderInterface, UserLoaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Humouriste::class);
    }

    public function loadUserByIdentifier(string $emailOuPseudo): ?Humouriste
    {
        $entityManager = $this->getEntityManager();
        return $entityManager->createQuery(
            'SELECT h
                FROM App\Entity\Humouriste h
                WHERE h.pseudo = :query
                OR h.email = :query'
        )
            ->setParameter('query', $emailOuPseudo)
            ->getOneOrNullResult();
    }

    public function add(Humouriste $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Humouriste $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof Humouriste) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->add($user, true);
    }

//    /**
//     * @return Humouriste[] Returns an array of Humouriste objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('h.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Humouriste
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    /**
     * @deprecated
     * @param string $username
     * @return \Symfony\Component\Security\Core\User\UserInterface|void|null
     */
    public function loadUserByUsername(string $username)
    {
        // TODO: Implement loadUserByUsername() method.
    }
}
