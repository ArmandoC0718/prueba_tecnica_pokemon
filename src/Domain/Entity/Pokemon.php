<?php

namespace App\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'pokemon')]
class Pokemon
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 100)]
    private string $name;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $nickname = null;

    #[ORM\Column(type: 'integer')]
    private int $level;

    #[ORM\Column(type: 'integer')]
    private int $health_points;

    #[ORM\Column(type: 'integer')]
    private int $attack;

    #[ORM\Column(type: 'integer')]
    private int $defense;

    #[ORM\Column(type: 'integer')]
    private int $speed;

    #[ORM\Column(type: 'integer')]
    private int $catch_rate;

    // Relacion ManyToOne con la entidad User
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'pokemons')]
    #[ORM\JoinColumn(name: 'trainer_id', referencedColumnName: 'id')]
    private ?User $trainer = null;

    // Relacion ManyToMany con la entidad Type 
    #[ORM\ManyToMany(targetEntity: Type::class, inversedBy: 'pokemons')]
    #[ORM\JoinTable(
        name: 'pokemon_type',
        joinColumns: [new ORM\JoinColumn(name: 'pokemon_id', referencedColumnName: 'id')],
        inverseJoinColumns: [new ORM\JoinColumn(name: 'type_id', referencedColumnName: 'id')]
    )]
    private Collection $types;

    // Relación ManyToMany con la entidad Move
    #[ORM\ManyToMany(targetEntity: Move::class, inversedBy: 'pokemons')]
    #[ORM\JoinTable(
        name: 'pokemon_move',
        joinColumns: [new ORM\JoinColumn(name: 'pokemon_id', referencedColumnName: 'id')],
        inverseJoinColumns: [new ORM\JoinColumn(name: 'move_id', referencedColumnName: 'id')]
    )]
    private Collection $moves;

    public function __construct()
    {
        $this->types = new ArrayCollection();
        $this->moves = new ArrayCollection();
    }

    public function getTypes(): Collection
    {
        return $this->types;
    }

    public function getMoves(): Collection
    {
        return $this->moves;
    }

    public function addType(Type $type): self
    {
        if (!$this->types->contains($type)) {
            $this->types[] = $type;
            $type->addPokemon($this);
        }
        return $this;
    }

    public function addMove(Move $move): self
    {
        if (!$this->moves->contains($move)) {
            // Validación: máximo 4 movimientos por Pokémon
            if ($this->moves->count() >= 4) {
                throw new \InvalidArgumentException('Un Pokémon no puede tener más de 4 movimientos');
            }

            $this->moves[] = $move;
            $move->addPokemon($this);
        }
        return $this;
    }

    public function removeType(Type $type): self
    {
        if ($this->types->removeElement($type)) {
            $type->removePokemon($this);
        }

        return $this;
    }

    public function removeMove(Move $move): self
    {
        if ($this->moves->removeElement($move)) {
            $move->removePokemon($this);
        }

        return $this;
    }


    // Getters 
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }
    public function getLevel(): ?int
    {
        return $this->level;
    }
    public function getHealthPoints(): ?int
    {
        return $this->health_points;
    }
    public function getAttack(): ?int
    {
        return $this->attack;
    }
    public function getDefense(): ?int
    {
        return $this->defense;
    }
    public function getSpeed(): ?int
    {
        return $this->speed;
    }
    public function getCatchRate(): ?int
    {
        return $this->catch_rate;
    }
    public function getTrainer(): ?User
    {
        return $this->trainer;
    }

    // Setters 

    public function setName(string $name): self
    {
        if (empty(trim($name))) {
            throw new \InvalidArgumentException("El nombre del pokemon no puede estar vacio.");
        }

        $this->name = $name;
        return $this;
    }

    public function setNickname(?string $nickname): self
    {
        $this->nickname = $nickname;
        return $this;
    }

    public function setLevel(int $level): self
    {
        $this->level = $level;
        return $this;
    }

    public function setHealthPoints(int $health_points): self
    {
        $this->health_points = $health_points;
        return $this;
    }

    public function setAttack(int $attack): self
    {
        $this->attack = $attack;
        return $this;
    }

    public function setDefense(int $defense): self
    {
        $this->defense = $defense;
        return $this;
    }

    public function setSpeed(int $speed): self
    {
        $this->speed = $speed;
        return $this;
    }

    public function setCatchRate(int $catch_rate): self
    {
        $this->catch_rate = $catch_rate;
        return $this;
    }

    public function setTrainer(?User $trainer): self
    {
        $this->trainer = $trainer;
        return $this;
    }
}