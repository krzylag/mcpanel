<?php

declare(strict_types=1);

namespace App\Dto;

use App\Entity\Tenant;
use ArrayAccess;
use ArrayIterator;
use Countable;
use Doctrine\ORM\Mapping\Entity;
use Exception;
use IteratorAggregate;
use Traversable;

class TenantsCollection implements Traversable, IteratorAggregate, Countable, ArrayAccess
{
    /** @var Tenant[] */
    private array $tenants;

    public function __construct(
        array $tenants = []
    ) {
        $this->tenants = $tenants;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->tenants);
    }

    public function getSingle(): ?Tenant
    {
        return count($this->tenants) === 1 ? $this->tenants[0] : null;
    }

    public function getFirstByPassword(string $password): ?Tenant
    {
        foreach ($this->tenants as $tenant) {
            if ($tenant->getPassword() === $password) {
                return $tenant;
            }
        }
        return null;
    }

    public function offsetExists(mixed $offset): bool
    {
        if (!is_int($offset)) {
            throw new Exception('offset must be an integer');
        }
        return array_key_exists($offset, $this->tenants);
    }

    public function offsetGet(mixed $offset): Entity
    {
        if (!is_int($offset)) {
            throw new Exception('offset must be an integer');
        }
        return $this->tenants[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (!is_int($offset)) {
            throw new Exception('offset must be an integer');
        }
        if(!$value instanceof Entity){
            throw new Exception('value must be an instance of Entity');
        }
        $this->tenants[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        if (!is_int($offset)) {
            throw new Exception('offset must be an integer');
        }
        unset($this->tenants[$offset]);
        $this->tenants = array_values($this->tenants);
    }

    public function count(): int
    {
        return count($this->tenants);
    }

    public function toArray(): array
    {
        return $this->tenants;
    }
}