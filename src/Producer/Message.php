<?php

declare(strict_types=1);

namespace Evirma\Bundle\KafkaBundle\Producer;

readonly class Message
{
    public function __construct(private string $payload, private ?string $key = null)
    {
    }

    public function getPayload(): string
    {
        return $this->payload;
    }

    public function getKey(): ?string
    {
        return $this->key;
    }
}
