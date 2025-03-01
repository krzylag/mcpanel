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

    #[ORM\Column(length: 255, nullable: false)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: false)]
    private ?string $mcHost = null;

    #[ORM\Column]
    private array $domains = [];

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

    public function getMcHost(): ?string
    {
        return $this->mcHost;
    }

    public function setMcHost(string $mcHost): static
    {
        $this->mcHost = $mcHost;

        return $this;
    }

    public function getDomains(): array
    {
        return $this->domains;
    }

    public function setDomains(array $domains): static
    {
        $domains = array_map('trim', $domains);
        sort($domains);
        $this->domains = array_values(array_unique($domains));
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
