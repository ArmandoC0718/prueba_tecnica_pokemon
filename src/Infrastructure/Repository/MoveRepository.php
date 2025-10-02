<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Move;
use App\Domain\Repository\MoveRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MoveRepository extends ServiceEntityRepository implements MoveRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Move::class);
    }

    public function findById(int $id): ?Move
    {
        return $this->find($id);
    }

    public function save(Move $move): void
    {
        $this->getEntityManager()->persist($move);
        $this->getEntityManager()->flush();
    }
}