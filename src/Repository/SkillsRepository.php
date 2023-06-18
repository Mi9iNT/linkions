<?php

namespace App\Repository;

use App\Entity\Skills;
use App\Entity\Users;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Skills>
 */
class SkillsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Skills::class);
    }

    public function save(Skills $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Skills $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findEachSkills()
    {
        return $this->createQueryBuilder('b')
            ->select('b')->distinct()
            ->orderBy('b.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findDuplicates(Users $user, array $skills): array
    {
        $queryBuilder = $this->createQueryBuilder('s')
            ->join('s.users', 'u')
            ->where('u = :user')
            ->andWhere('s IN (:skills)')
            ->setParameter('user', $user)
            ->setParameter('skills', $skills);

        return $queryBuilder->getQuery()->getResult();
    }
}
