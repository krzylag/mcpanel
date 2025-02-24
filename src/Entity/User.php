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
    public const string ROLE_ADMIN = 'ROLE_ADMIN';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $username = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    /**
     * @var Collection<int, TenantUser>
     */
    #[ORM\OneToMany(targetEntity: TenantUser::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $tenantUsers;

    public function __construct()
    {
        $this->tenantUsers = new ArrayCollection();
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
        $roles = $this->roles;
        $roles[] = self::ROLE_USER;
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
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
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, TenantUser>
     */
    public function getTenantUsers(): Collection
    {
        return $this->tenantUsers;
    }

    public function addTenantUser(TenantUser $tenantUser): static
    {
        if (!$this->tenantUsers->contains($tenantUser)) {
            $this->tenantUsers->add($tenantUser);
            $tenantUser->setUser($this);
        }

        return $this;
    }

    public function removeTenantUser(TenantUser $tenantUser): static
    {
        if ($this->tenantUsers->removeElement($tenantUser)) {
            // set the owning side to null (unless already changed)
            if ($tenantUser->getUser() === $this) {
                $tenantUser->setUser(null);
            }
        }

        return $this;
    }
}
