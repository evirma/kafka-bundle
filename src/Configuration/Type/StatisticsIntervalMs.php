<?php

declare(strict_types=1);

namespace Evirma\Bundle\KafkaBundle\Configuration\Type;

use Evirma\Bundle\KafkaBundle\Contract\CastValueInterface;
use Evirma\Bundle\KafkaBundle\Contract\ConsumerConfigurationInterface;
use Evirma\Bundle\KafkaBundle\Contract\KafkaConfigurationInterface;
use Symfony\Component\Console\Input\InputOption;

class StatisticsIntervalMs implements ConsumerConfigurationInterface, KafkaConfigurationInterface, CastValueInterface
{
    public const NAME = 'statistics_interval_ms';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getKafkaProperty(): string
    {
        return 'statistics.interval.ms';
    }

    public function getMode(): int
    {
        return InputOption::VALUE_REQUIRED;
    }

    public function getDescription(): string
    {
        return sprintf(
            <<<EOT
            The frequency in milliseconds that the internal rdkafka statistics are sent to client.
            Defaults to %s ms. Set 0 to disable.
            EOT,
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
