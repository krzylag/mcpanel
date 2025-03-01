<?php

namespace App\Entity;

use App\Entity\Trait\TimestampableEntityTrait;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use TimestampableEntityTrait;

    public const string ROLE_USER = 'ROLE_USER';
    public const string ROLE_PLAYER = 'ROLE_PLAYER';
    public const string ROLE_ADMIN = 'ROLE_ADMIN';
    public const string ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $username = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column(nullable: true)]
    private ?string $password = null;

    /**
     * @var Collection<int, Tenant>
     */
    #[ORM\ManyToMany(targetEntity: Tenant::class, inversedBy: 'users', fetch: 'EAGER')]
    private Collection $tenants;

    public function __construct()
    {
        $this->tenants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): static
    {
        if (!in_array(self::ROLE_USER, $roles)) {
            $roles[] = self::ROLE_USER;
        }
        sort($roles);
        $this->roles = array_values(array_unique($roles));
        return $this;
    }

    public function addRole(string $role): static
    {
        $this->setRoles(
            array_merge($this->getRoles(), [$role])
        );
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): static
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Tenant>
     */
    public function getTenants(): Collection
    {
        return $this->tenants;
    }

    public function addTenant(Tenant $tenant): static
    {
        if (!$this->tenants->contains($tenant)) {
            $this->tenants->add($tenant);
        }

        return $this;
    }

    public function removeTenant(Tenant $tenant): static
    {
        $this->tenants->removeElement($tenant);

        return $this;
    }
}
