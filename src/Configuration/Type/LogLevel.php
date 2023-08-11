<?php

declare(strict_types=1);

namespace Evirma\Bundle\KafkaBundle\Configuration\Type;

use Evirma\Bundle\KafkaBundle\Configuration\Contract\CastValueInterface;
use Evirma\Bundle\KafkaBundle\Configuration\Contract\ConsumerConfigurationInterface;
use Evirma\Bundle\KafkaBundle\Configuration\Contract\KafkaConfigurationInterface;
use Evirma\Bundle\KafkaBundle\Configuration\Contract\ProducerConfigurationInterface;
use Symfony\Component\Console\Input\InputOption;

class LogLevel implements KafkaConfigurationInterface, ConsumerConfigurationInterface, ProducerConfigurationInterface, CastValueInterface
{
    public const NAME = 'log_level';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getKafkaProperty(): string
    {
        return 'log_level';
    }

    public function getMode(): int
    {
        return InputOption::VALUE_REQUIRED;
    }

    public function getDescription(): string
    {
        return sprintf(
            'Logging level (syslog(3) levels). Defaults to LOG_ERR (%s)',
            $this->getDefaultValue()
        );
    }

    public function isValueValid(mixed $value): bool
    {
        return is_numeric($value) && !str_contains((string)$value, '.');
    }

    public function cast(mixed $validatedValue): int
    {
        return (int) $validatedValue;
    }

    public function getDefaultValue(): int
    {
        return LOG_ERR;
    }
}
