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

}
