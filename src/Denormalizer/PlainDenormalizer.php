<?php

declare(strict_types=1);

namespace Evirma\Bundle\KafkaBundle\Denormalizer;

use Evirma\Bundle\KafkaBundle\Contract\DenormalizerInterface;

class PlainDenormalizer implements DenormalizerInterface
{
    public function denormalize(mixed $data): mixed
    {
        return $data;
    }
}
