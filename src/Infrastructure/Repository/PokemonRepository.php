<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Pokemon;
use App\Domain\Repository\PokemonRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


class PokemonRepository extends ServiceEntityRepository implements PokemonRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pokemon::class);
    }

    public function findRandomWildPokemon(): ?Pokemon
    {
        $pokeNotTrainer = $this->createQueryBuilder('poke')
            ->where('poke.trainer IS NULL')
            ->orderBy('RAND()') // Ordena aleatoriamente
            ->setMaxResults(1);
        
        return $pokeNotTrainer->getQuery()->getOneOrNullResult();
    }

    public function save(Pokemon $pokemon): void
    {
        $this->getEntityManager()->persist($pokemon);
        $this->getEntityManager()->flush();
    }
}