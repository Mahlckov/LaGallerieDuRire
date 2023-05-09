<?php

namespace App\Repository;

use App\Data\SearchData;
use App\Entity\Blague;
use App\Entity\Humouriste;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Blague>
 *
 * @method Blague|null find($id, $lockMode = null, $lockVersion = null)
 * @method Blague|null findOneBy(array $criteria, array $orderBy = null)
 * @method Blague[]    findAll()
 * @method Blague[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlagueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Blague::class);
    }

    public function findBlagueById(int $id)
    {
        $query = $this->createQueryBuilder('b')
            ->select('b','humouristes')
            ->leftJoin('b.humouriste', 'humouristes');

        $query->andWhere('b.id = :id')
            ->setParameter('id', $id);
        $query->orderBy('b.datePublication', 'DESC');
        return $query->getQuery()->getResult();
    }

    public function findBlagues(SearchData $data)
    {
        $query = $this->createQueryBuilder('b')
            ->select('b')
            ->leftJoin('b.humouriste', 'humouristes');

        //Si le paramètre de "recherche" est rempli
        if (!empty($data->q)) {
            $query = $query
                ->andWhere('b.libelle LIKE :q OR b.description LIKE :q OR humouristes.pseudo LIKE :q')
                ->setParameter('q', "%{$data->q}%");
            $query->orderBy('b.datePublication', 'DESC');
        }
        return $query->getQuery()->getResult();
    }



    public function add(Blague $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Blague $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Blague[] Returns an array of Blague objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Blague
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
