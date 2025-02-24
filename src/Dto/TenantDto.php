<?php

declare(strict_types=1);

namespace App\Dto;

readonly class TenantDto
{
    public function __construct(
        private string $name,
        private string $host,
        private string $domain,
        private int    $rconPort,
        private string $rconPassword,
        private int    $webconsoleSocketPort,
        private int    $bedrockPort,
        private int    $javaPort,
        private string $registrationPassword,
        private string $translationKey,
    ) {
    }

    public static function fromConfiguration(array $configuration): self
    {
        return new self(
            $configuration['name'],
            $configuration['host'],
            $configuration['domain'],
            $configuration['rcon']['port'],
            $configuration['rcon']['password'],
            $configuration['webconsole']['socket_port'],
            $configuration['server']['bedrock_port'],
            $configuration['server']['java_port'],
            $configuration['registration']['password'],
            sprintf('tenant.%s.name', $configuration['name']),
        );
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function getRconPort(): int
    {
        return $this->rconPort;
    }

    public function getRconPassword(): string
    {
        return $this->rconPassword;
    }

    public function getWebconsoleSocketPort(): int
    {
        return $this->webconsoleSocketPort;
    }

    public function getBedrockPort(): int
    {
        return $this->bedrockPort;
    }

    public function getJavaPort(): int
    {
        return $this->javaPort;
    }

    public function getRegistrationPassword(): string
    {
        return $this->registrationPassword;
    }

    public function getTranslationKey(): string
    {
        return $this->translationKey;
    }
}