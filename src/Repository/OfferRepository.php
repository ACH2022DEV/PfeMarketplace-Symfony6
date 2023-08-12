<?php

namespace App\Repository;

use App\Entity\Offer;
use App\Entity\OfferProductType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Offer>
 *
 * @method Offer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Offer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Offer[]    findAll()
 * @method Offer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OfferRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Offer::class);
    }

    public function save(Offer $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Offer $entity, bool $flush = false): void
    {

        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        try {
            $qb->delete(OfferProductType::class, 'opt')
                ->where('opt.offer = :offer')
                ->setParameter('offer', $entity)
                ->getQuery()
                ->execute();

            $em->remove($entity);
            if ($flush) {
                $em->flush();
            }
        } catch (ForeignKeyConstraintViolationException $e) {
            // Handle the exception
            throw new \Exception($e->getMessage());
        }
    }

    public function getSumPricesByProductType($offerId)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();

        $queryBuilder
            ->select('opt.type, SUM(opt.price) as total')
            ->from('AppBundle:OfferProductType', 'opt')
            ->where('opt.offer = :offerId')
            ->groupBy('opt.type')
            ->setParameter('offerId', $offerId);

        return $queryBuilder->getQuery()->getResult();
    }

    public function searchByName($name)
    {
        $qb = $this->createQueryBuilder('o')
            ->where('o.name LIKE :name')
            ->setParameter('name', '%'.$name.'%')
            ->getQuery();

        return $qb->getResult();
    }

//    /**
//     * @return Offer[] Returns an array of Offer objects
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

//    public function findOneBySomeField($value): ?Offer
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
