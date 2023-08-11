<?php

declare(strict_types=1);

namespace Evirma\Bundle\KafkaBundle\Contract;

interface DenormalizerInterface
{
    public function denormalize(mixed $data): mixed;
}
