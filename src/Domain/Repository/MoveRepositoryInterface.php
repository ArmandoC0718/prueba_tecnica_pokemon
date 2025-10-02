<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Move;

interface MoveRepositoryInterface
{
    public function findById(int $id): ?Move;
    public function save(Move $move): void;
}