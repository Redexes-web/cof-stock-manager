<?php

namespace App\Repository;

use App\Entity\CofProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CofProduct>
 *
 * @method CofProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method CofProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method CofProduct[]    findAll()
 * @method CofProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CofProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CofProduct::class);
    }

    public function add(CofProduct $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CofProduct $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function findLikeName(?string $name, ?string $supplierId = null)
    {
        dump($name, $supplierId);
        return $this->createQueryBuilder('p')
            ->leftJoin('p.stocks', 's')
            ->leftJoin('s.supplier', 'sp')
            ->andWhere('p.name LIKE :name' . ($supplierId ? ' AND sp.id = :supplierId' : ''))
            ->setParameter('name', '%' . $name . '%')
            ->setParameter('supplierId', $supplierId)
            ->getQuery()
            ->getResult();
    
    }
//    /**
//     * @return CofProduct[] Returns an array of CofProduct objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CofProduct
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
