<?php

namespace App\Repository;

use App\Entity\MessageSend;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method MessageSend|null find($id, $lockMode = null, $lockVersion = null)
 * @method MessageSend|null findOneBy(array $criteria, array $orderBy = null)
 * @method MessageSend[]    findAll()
 * @method MessageSend[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageSendRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MessageSend::class);
    }

    // /**
    //  * @return MessageSend[] Returns an array of MessageSend objects
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
    public function findOneBySomeField($value): ?MessageSend
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
