<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid')]
    #[ORM\CustomIdGenerator(class: 'Ramsey\Uuid\Doctrine\UuidGenerator')]
    private ?UuidInterface $id = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\ManyToMany(targetEntity: Group::class, mappedBy: 'users')]
    private Collection $passwordGroups;

    #[ORM\OneToMany(targetEntity: Password::class, mappedBy: 'owner')]
    private Collection $passwords;

    public function __construct()
    {
        $this->passwordGroups = new ArrayCollection();
        $this->passwords = new ArrayCollection();
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return Collection<int, Group>
     */
    public function getPasswordGroups(): Collection
    {
        return $this->passwordGroups;
    }

    public function addPasswordGroup(Group $passwordGroup): static
    {
        if (!$this->passwordGroups->contains($passwordGroup)) {
            $this->passwordGroups->add($passwordGroup);
            $passwordGroup->addUser($this);
        }

        return $this;
    }

    public function removePasswordGroup(Group $passwordGroup): static
    {
        if ($this->passwordGroups->removeElement($passwordGroup)) {
            $passwordGroup->removeUser($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Password>
     */
    public function getPasswords(): Collection
    {
        return $this->passwords;
    }

    public function addPassword(Password $password): static
    {
        if (!$this->passwords->contains($password)) {
            $this->passwords->add($password);
            $password->setOwner($this);
        }

        return $this;
    }

    public function removePassword(Password $password): static
    {
        if ($this->passwords->removeElement($password)) {
            // set the owning side to null (unless already changed)
            if ($password->getOwner() === $this) {
                $password->setOwner(null);
            }
        }

        return $this;
    }
}
