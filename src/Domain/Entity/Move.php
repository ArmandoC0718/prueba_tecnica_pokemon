<?php

namespace App\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use stdClass;

#[ORM\Entity]
#[ORM\Table(name: 'move')]
class Move {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 100, unique: true)]
    private string $name;

    // Relacion ManyToOne con la entidad Type 
    #[ORM\ManyToOne(targetEntity: Type::class, inversedBy: 'moves')]
    #[ORM\JoinColumn(name: 'type_id', referencedColumnName: 'id')]
    private ?Type $type = null;

    // Getters

    public function getId(): ?int {
        return $this->id;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function getType(): ?Type {
        return $this->type;
    }

    // Setters

    public function setName(string $name): self {

        if (empty(trim($name))) {
            throw new \InvalidArgumentException("El nombre del movimiento no puede estar vacio.");
        }

        $this->name = $name;
        return $this;
    }

    public function setType(?Type $type): self {
        $this->type = $type;
        return $this;
    }

}