<?php

namespace App\Repository;

use App\Entity\OfferProductType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
* @extends ServiceEntityRepository<OfferProductType>
    *
    * @method OfferProductType|null find($id, $lockMode = null, $lockVersion = null)
    * @method OfferProductType|null findOneBy(array $criteria, array $orderBy = null)
    * @method OfferProductType[]    findAll()
    * @method OfferProductType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    */
    class OfferProductTypeRepository extends ServiceEntityRepository
    {
    public function __construct(ManagerRegistry $registry)
    {
    parent::__construct($registry, OfferProductType::class);
    }

    public function save(OfferProductType $entity, bool $flush = false): void
    {
    $this->getEntityManager()->persist($entity);

    if ($flush) {
    $this->getEntityManager()->flush();
    }
    }

    public function remove(OfferProductType $entity, bool $flush = false): void
    {
    $this->getEntityManager()->remove($entity);

    if ($flush) {
    $this->getEntityManager()->flush();
    }
    }

    //    /**
    //     * @return OfferProductType[] Returns an array of OfferProductType objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('o.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?OfferProductType
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    }
