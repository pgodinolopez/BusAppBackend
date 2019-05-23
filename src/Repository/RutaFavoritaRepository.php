<?php

namespace App\Repository;

use App\Entity\RutaFavorita;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method RutaFavorita|null find($id, $lockMode = null, $lockVersion = null)
 * @method RutaFavorita|null findOneBy(array $criteria, array $orderBy = null)
 * @method RutaFavorita[]    findAll()
 * @method RutaFavorita[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RutaFavoritaRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, RutaFavorita::class);
    }

    // /**
    //  * @return RutaFavorita[] Returns an array of RutaFavorita objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Cita
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
