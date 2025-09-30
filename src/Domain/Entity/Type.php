<?php

namespace App\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'type')]
class Type
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 100, unique: true)]
    private string $name;

    // Getters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    // Setters

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    #[ORM\OneToMany(mappedBy: 'type', targetEntity: Move::class)]
    private Collection $moves;

    public function __construct()
    {
        $this->moves = new ArrayCollection();
        $this->pokemons = new ArrayCollection();
    }


    public function getMoves(): Collection
    {
        return $this->moves;
    }

    public function addMove(Move $move): self
    {
        // Validar si el pokemon ya esta en la coleccion
        if (!$this->moves->contains($move)) {
            $this->moves[] = $move;
            $move->setType($this);
        }
        return $this;
    }

    public function removeMove(Move $move): self
    {
        if (!$this->moves->removeElement($move)) {
            if ($move->getType() === $this) {
                $move->setType(null);
            }
        }
        return $this;
    }

}