<?php

namespace App\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity]
#[ORM\Table(name: 'user')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private string $username;

    #[ORM\Column(type: 'string', length: 255)]
    private string $password;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    // Getters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {

        if (empty(trim($this->username))) {
            throw new \InvalidArgumentException("El nombre de usuario no puede estar vacio.");
        }

        return $this->username;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    // Setters

    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    // Relacion OneToMany con Pokemon
    #[ORM\OneToMany(mappedBy: 'trainer', targetEntity: Pokemon::class)]
    private Collection $pokemons;

    public function __construct()
    {
        $this->pokemons = new ArrayCollection();
        $this->roles = [];
    }

    public function getPokemos(): Collection
    {
        return $this->pokemons;
    }

    public function addPokemon(Pokemon $pokemon): self
    {
        // Validar si el pokémon ya está en la colección
        if (!$this->pokemons->contains($pokemon)) {
            $this->pokemons[] = $pokemon;
            $pokemon->setTrainer($this);
        }
        return $this;
    }

    public function removePokemon(Pokemon $pokemon): self
    {
        // Remover el pokemón de la colección
        if (!$this->pokemons->removeElement($pokemon)) {
            if ($pokemon->getTrainer() === $this) {
                $pokemon->setTrainer(null);
            }
        }
        return $this;
    }

}