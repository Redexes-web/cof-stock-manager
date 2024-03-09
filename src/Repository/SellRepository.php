<?php

namespace App\Repository;

use App\Entity\Supplier;
use App\Entity\Sell;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sell>
 *
 * @method Sell|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sell|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sell[]    findAll()
 * @method Sell[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SellRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sell::class);
    }

    public function add(Sell $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Sell $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    // findBySupplier($supplier,$page)

    public function findBySupplierQuery(Supplier $supplier, string $sort, string $direction, ?string $month): \Doctrine\ORM\Query
    {
        $q = $this->createQueryBuilder('s')
            ->leftJoin('s.product', 'p')
            ->andWhere('s.supplier = :supplier');
        if ($month) {
            $q->andWhere('DATE_FORMAT(s.soldAt, \'%Y-%m\') = :month')
                ->setParameter('month', $month);
        }
        $q->orderBy($sort, $direction)
            ->setParameter('supplier', $supplier);

        return $q->getQuery();
    }

    public function sellsPerMonthBySupplier(Supplier $supplier)
    {
        $qb = $this->createQueryBuilder('s');
        $qb->select('DISTINCT DATE_FORMAT(s.soldAt, \'%Y-%m\') AS month, COUNT(s.id) AS nbSells')
            ->andWhere('s.supplier = :supplier')
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->setParameter('supplier', $supplier);

        $results = $qb->getQuery()->getResult();

        $monthSells = array_map(function ($result) {
            return [
                'month' => \DateTime::createFromFormat('Y-m', $result['month']),
                'nbSells' => $result['nbSells']
            ];
        }, $results);

        return $monthSells;
    }
    //    /**
    //     * @return Sell[] Returns an array of Sell objects
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

    //    public function findOneBySomeField($value): ?Sell
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
