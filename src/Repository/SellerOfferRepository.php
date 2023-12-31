<?php

namespace App\Repository;

use App\Entity\Seller;
use App\Entity\SellerOffer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SellerOffer>
 *
 * @method SellerOffer|null find($id, $lockMode = null, $lockVersion = null)
 * @method SellerOffer|null findOneBy(array $criteria, array $orderBy = null)
 * @method SellerOffer[]    findAll()
 * @method SellerOffer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SellerOfferRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SellerOffer::class);
    }

    public function save(SellerOffer $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SellerOffer $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function findExistingSellerOffer($sellerId, $offer): ?SellerOffer
    {
        $qb = $this->createQueryBuilder('so')
            ->andWhere('so.seller = :sellerId')
            ->andWhere('so.offer = :offer')
            ->setParameter('sellerId', $sellerId)
            ->setParameter('offer', $offer)
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }
    /*public function findSellerByUserId(int $userId): ?Seller
    {
        return $this->createQueryBuilder('s')
            ->join('s.user', 'u')
            ->andWhere('u.id = :user_id')
            ->setParameter('user_id', $userId)
            ->getQuery()
            ->getOneOrNullResult();
    }*/

//    /**
//     * @return SellerOffer[] Returns an array of SellerOffer objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?SellerOffer
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
