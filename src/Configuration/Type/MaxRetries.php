<?php

declare(strict_types=1);

namespace Evirma\Bundle\KafkaBundle\Configuration\Type;

use Evirma\Bundle\KafkaBundle\Configuration\Contract\CastValueInterface;
use Evirma\Bundle\KafkaBundle\Configuration\Contract\ConsumerConfigurationInterface;
use Symfony\Component\Console\Input\InputOption;

class MaxRetries implements ConsumerConfigurationInterface, CastValueInterface
{
    public const NAME = 'max_retries';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getMode(): int
    {
        return InputOption::VALUE_REQUIRED;
    }

    public function getDescription(): string
    {
        return sprintf(
            'How many times message should be consumed if exception is thrown. Defaults to %s',
            $this->getDefaultValue()
        );
    }

    public function isValueValid(mixed $value): bool
    {
        return is_numeric($value) && !str_contains((string)$value, '.') && $value >= 0;
    }

    public function getDefaultValue(): int
    {
        return 0;
    }

    public function cast(mixed $validatedValue): int
    {
        return (int) $validatedValue;
    }
}
