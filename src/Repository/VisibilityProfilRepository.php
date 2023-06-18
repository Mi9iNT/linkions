<?php

namespace App\Repository;

use App\Entity\VisibilityProfil;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<VisibilityProfil>
 *
 * @method VisibilityProfil|null find($id, $lockMode = null, $lockVersion = null)
 * @method VisibilityProfil|null findOneBy(array $criteria, array $orderBy = null)
 * @method VisibilityProfil[]    findAll()
 * @method VisibilityProfil[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VisibilityProfilRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VisibilityProfil::class);
    }

    public function save(VisibilityProfil $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(VisibilityProfil $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return VisibilityProfil[] Returns an array of VisibilityProfil objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('v.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?VisibilityProfil
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
