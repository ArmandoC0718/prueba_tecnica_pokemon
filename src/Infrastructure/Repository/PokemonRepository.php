<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Pokemon;
use App\Domain\Entity\User;
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

    public function findByTrainer(User $trainer): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.trainer = :trainer')
            ->setParameter('trainer', $trainer)
            ->orderBy('p.nickname', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findById(int $id): ?Pokemon
    {
        return $this->find($id);
    }

    public function save(Pokemon $pokemon): void
    {
        $this->getEntityManager()->persist($pokemon);
        $this->getEntityManager()->flush();
    }
}