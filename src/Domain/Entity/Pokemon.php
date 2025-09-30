<?php

namespace App\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'pokemon')]
class Pokemon {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 100)]
    private string $name;

    #[ORM\Column(type: 'string', length: 100)]
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

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'pokemons')]
    #[ORM\JoinColumn(name: 'trainer_id', referencedColumnName: 'id')]
    private ?User $trainer = null;


    // Getters 
    public function getId(): ?int {
        return $this->id;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function getNickname(): ?string {
        return $this->nickname;
    }
    public function getLevel(): ?int {
        return $this->level;
    }
    public function getHealthPoints(): ?int {
        return $this->health_points;
    }
    public function getAttack(): ?int {
        return $this->attack;
    }
    public function getDefense(): ?int {
        return $this->defense;
    }
    public function getSpeed(): ?int {
        return $this->speed;
    }
    public function getCatchRate(): ?int {
        return $this->catch_rate;
    }
    public function getTrainer(): ?User {
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

    public function setNickname(?string $nickname): self {
        $this->nickname = $nickname;
        return $this;
    }

    public function setLevel(int $level): self {
        $this->level = $level;
        return $this;
    }

    public function setHealthPoints(int $health_points): self {
        $this->health_points = $health_points;
        return $this;
    }

    public function setAttack(int $attack): self {
        $this->attack = $attack;
        return $this;
    }

    public function setDefense(int $defense): self {
        $this->defense = $defense;
        return $this;
    }

    public function setSpeed(int $speed): self {
        $this->speed = $speed;
        return $this;
    }

    public function setCatchRate(int $catch_rate): self {
        $this->catch_rate = $catch_rate;
        return $this;
    }

    public function setTrainer(?User $trainer): self {
        $this->trainer = $trainer;
        return $this;
    }
}