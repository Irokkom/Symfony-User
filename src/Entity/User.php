<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null; 

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = []; 

    #[ORM\Column]
    private ?string $password = null; 

    #[ORM\Column(length: 50)]
    private ?string $name = null; 

    public function getId(): ?int { return $this->id; }

    public function getEmail(): ?string { return $this->email; }

    public function setEmail(string $email): self { 
        $this->email = $email;
        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): self { 
        $this->roles = $roles; 
        return $this; 
    }

    public function getPassword(): ?string { return $this->password; }

    public function setPassword(string $password): self { 
        $this->password = $password;
        return $this; 
    }

    public function getName(): ?string { return $this->name; }

    public function setName(string $name): self { 
        $this->name = $name;
        return $this; 
    }

    public function eraseCredentials(): void 
    {
    }

    public function getUserIdentifier(): string 
    {
        return $this->email;
    }
}