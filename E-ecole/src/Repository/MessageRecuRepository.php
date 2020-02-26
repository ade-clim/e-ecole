<?php

namespace App\Repository;

use App\Entity\MessageRecu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method MessageRecu|null find($id, $lockMode = null, $lockVersion = null)
 * @method MessageRecu|null findOneBy(array $criteria, array $orderBy = null)
 * @method MessageRecu[]    findAll()
 * @method MessageRecu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRecuRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MessageRecu::class);
    }

    // /**
    //  * @return MessageRecu[] Returns an array of MessageRecu objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MessageRecu
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
