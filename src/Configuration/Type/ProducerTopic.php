<?php

declare(strict_types=1);

namespace Evirma\Bundle\KafkaBundle\Configuration\Type;

use Evirma\Bundle\KafkaBundle\Contract\ProducerConfigurationInterface;
use Symfony\Component\Console\Input\InputOption;

class ProducerTopic implements ProducerConfigurationInterface
{
    public const NAME = 'producer_topic';

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
        return 'Producer topic name.';
    }

    public function isValueValid(mixed $value): bool
    {
        return is_string($value) && '' !== $value;
    }

    public function getDefaultValue(): string
    {
        return 'sts_gaming_group_kafka_producer_topic';
    }
}
