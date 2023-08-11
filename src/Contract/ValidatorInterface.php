<?php

declare(strict_types=1);

namespace Evirma\Bundle\KafkaBundle\Contract;

interface ValidatorInterface
{
    public function validate(mixed $data): bool;
    public function failureReason(mixed $data): string;
    public function type(): string;
}
