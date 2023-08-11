<?php

declare(strict_types=1);

namespace Evirma\Bundle\KafkaBundle\Configuration\Contract;

interface CastValueInterface extends ConfigurationInterface
{
    public function cast(mixed $validatedValue): mixed;
}
