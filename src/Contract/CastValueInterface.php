<?php

declare(strict_types=1);

namespace Evirma\Bundle\KafkaBundle\Contract;

interface CastValueInterface extends ConfigurationInterface
{
    public function cast(mixed $validatedValue): mixed;
}
