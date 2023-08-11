<?php

declare(strict_types=1);

namespace Evirma\Bundle\KafkaBundle\Configuration\Type;

use Evirma\Bundle\KafkaBundle\Configuration\Contract\ProducerConfigurationInterface;
use Symfony\Component\Console\Input\InputOption;

class ProducerPartition implements ProducerConfigurationInterface
{
    public const NAME = 'producer_partition';

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
        return
            <<<EOT
            Which partition producer should produce to. 
            Defaults to RD_KAFKA_PARTITION_UA (-1) and lets librdkafka choose the partition according to message key value.
            EOT;
    }

    public function isValueValid(mixed $value): bool
    {
        return (is_numeric($value) && !str_contains((string)$value, '.') && $value >= 0) ||
            $value === $this->getDefaultValue();
    }

    public function getDefaultValue(): int
    {
        return defined('RD_KAFKA_PARTITION_UA') ? RD_KAFKA_PARTITION_UA : -1;
    }
}
