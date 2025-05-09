<?php

namespace App\Entity;

use App\Dto\TenantDto;
use App\Entity\Trait\TimestampableEntityTrait;
use App\Provider\TenantProvider;
use App\Repository\TenantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TenantRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_TENANT_NAME', fields: ['name'])]
class Tenant
{
    use TimestampableEntityTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: false)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: false)]
    private ?string $mcTenantId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $registrationPassword = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'tenants')]
    private Collection $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getMcTenantId(): ?string
    {
        return $this->mcTenantId;
    }

    public function setMcTenantId(?string $mcTenantId): static
    {
        $this->mcTenantId = $mcTenantId;
        return $this;
    }

    public function getMcTenant(TenantProvider $tenantProvider): ?TenantDto
    {
        return $tenantProvider->getById($this->mcTenantId);
    }

    public function setMcTenant(TenantDto $mcTenant): static
    {
        $this->mcTenantId = $mcTenant->getHost();
        return $this;
    }

    public function getRegistrationPassword(): ?string
    {
        return $this->registrationPassword;
    }

    public function setRegistrationPassword(string $registrationPassword): static
    {
        $this->registrationPassword = $registrationPassword;

        return $this;
    }

    /** @return Collection<int, User> */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addTenant($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            $user->removeTenant($this);
        }

        return $this;
    }
}
