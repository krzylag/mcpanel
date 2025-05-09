<?php

declare(strict_types=1);

namespace App\Dto;

use ArrayAccess;
use ArrayIterator;
use Countable;
use Doctrine\ORM\Mapping\Entity;
use Exception;
use IteratorAggregate;
use Traversable;

class TenantCollection implements Traversable, IteratorAggregate, Countable, ArrayAccess
{
    /** @var TenantDto[] */
    private array $tenants = [];

    /** @param TenantDto[] $tenants */
    public function __construct(array $tenants = [])
    {
        foreach ($tenants as $tenant) {
            $this->add($tenant);
        }
    }

    public function add(TenantDto $tenantDto): static
    {
        $this->tenants[$tenantDto->getHost()] = $tenantDto;
        return $this;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->tenants);
    }

    public function count(): int
    {
        return count($this->tenants);
    }

    public function offsetExists(mixed $offset): bool
    {
        if (!is_int($offset)) {
            throw new Exception('offset must be an integer');
        }
        return array_key_exists($offset, $this->tenants);
    }

    public function offsetGet(mixed $offset): TenantDto
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

    public function toArray(): array
    {
        return $this->tenants;
    }
}