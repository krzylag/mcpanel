<?php

namespace App\Entity;

use App\Entity\Trait\TimestampableEntityTrait;
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

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $host = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $externalDomain = null;

    #[ORM\Column(nullable: true)]
    private ?int $rconPort = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $rconPassword = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $registrationPassword = null;

    #[ORM\Column(nullable: true)]
    private ?int $bedrockPort = null;

    #[ORM\Column(nullable: true)]
    private ?int $javaPort = null;

    /**
     * @var Collection<int, TenantUser>
     */
    #[ORM\OneToMany(targetEntity: TenantUser::class, mappedBy: 'tenant', orphanRemoval: true)]
    private Collection $tenantUsers;

    public function __construct()
    {
        $this->tenantUsers = new ArrayCollection();
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

    public function getHost(): ?string
    {
        return $this->host;
    }

    public function setHost(string $host): static
    {
        $this->host = $host;

        return $this;
    }

    public function getExternalDomain(): ?string
    {
        return $this->externalDomain;
    }

    public function setExternalDomain(string $externalDomain): static
    {
        $this->externalDomain = $externalDomain;

        return $this;
    }

    public function getRconPort(): ?int
    {
        return $this->rconPort;
    }

    public function setRconPort(int $rconPort): static
    {
        $this->rconPort = $rconPort;

        return $this;
    }

    public function getRconPassword(): ?string
    {
        return $this->rconPassword;
    }

    public function setRconPassword(string $rconPassword): static
    {
        $this->rconPassword = $rconPassword;

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

    public function getBedrockPort(): ?int
    {
        return $this->bedrockPort;
    }

    public function setBedrockPort(?int $bedrockPort): void
    {
        $this->bedrockPort = $bedrockPort;
    }

    public function getJavaPort(): ?int
    {
        return $this->javaPort;
    }

    public function setJavaPort(?int $javaPort): void
    {
        $this->javaPort = $javaPort;
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
            $tenantUser->setTenant($this);
        }

        return $this;
    }

    public function removeTenantUser(TenantUser $tenantUser): static
    {
        if ($this->tenantUsers->removeElement($tenantUser)) {
            // set the owning side to null (unless already changed)
            if ($tenantUser->getTenant() === $this) {
                $tenantUser->setTenant(null);
            }
        }

        return $this;
    }
}
