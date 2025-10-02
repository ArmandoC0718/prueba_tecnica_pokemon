<?php

namespace App\Application\DTO;

class CapturePokemonRequestDTO
{
    public function __construct(
        public readonly ?string $nickname = null
    ) {}
}