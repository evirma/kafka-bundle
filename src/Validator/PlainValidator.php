<?php

declare(strict_types=1);

namespace Evirma\Bundle\KafkaBundle\Validator;

use Evirma\Bundle\KafkaBundle\Contract\ValidatorInterface;

class PlainValidator implements ValidatorInterface
{
    public function validate(mixed $data): bool
    {
        return true;
    }

    public function failureReason(mixed $data): string
    {
        return '';
    }

    public function type(): string
    {
        return Validator::PRE_DENORMALIZE_TYPE;
    }
}
