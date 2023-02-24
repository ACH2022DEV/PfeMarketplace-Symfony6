<?php

namespace App\Repository;

use App\Entity\MarketSubscriptionRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MarketSubscriptionRequest>
 *
 * @method MarketSubscriptionRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarketSubscriptionRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarketSubscriptionRequest[]    findAll()
 * @method MarketSubscriptionRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarketSubscriptionRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketSubscriptionRequest::class);
    }

    public function save(MarketSubscriptionRequest $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(MarketSubscriptionRequest $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return MarketSubscriptionRequest[] Returns an array of MarketSubscriptionRequest objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?MarketSubscriptionRequest
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
