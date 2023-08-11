<?php

declare(strict_types=1);

namespace Evirma\Bundle\KafkaBundle\Configuration\Traits;

use Symfony\Component\Console\Input\InputOption;

trait BooleanConfigurationTrait
{
    public function getMode(): int
    {
        return InputOption::VALUE_REQUIRED;
    }

    public function isValueValid(mixed $value): bool
    {
        return in_array($value, ['true', 'false'], true);
    }

    public function cast(mixed $validatedValue): bool
    {
        return match ($validatedValue) {
            'true' => true,
            'false' => false,
            default => (bool)$validatedValue,
        };
    }
}
