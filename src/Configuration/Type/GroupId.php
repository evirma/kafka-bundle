<?php

declare(strict_types=1);

namespace Evirma\Bundle\KafkaBundle\Configuration\Type;

use Evirma\Bundle\KafkaBundle\Contract\ConsumerConfigurationInterface;
use Evirma\Bundle\KafkaBundle\Contract\KafkaConfigurationInterface;
use Symfony\Component\Console\Input\InputOption;

class GroupId implements KafkaConfigurationInterface, ConsumerConfigurationInterface
{
    public const NAME = 'group_id';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getMode(): int
    {
        return InputOption::VALUE_REQUIRED;
    }

    public function getKafkaProperty(): string
    {
        return 'group.id';
    }

    public function getDescription(): string
    {
        return <<<EOT
        Client group id string. All clients sharing the same group.id belong to the same group. 
        Must be a non empty string
        EOT;
    }

    public function isValueValid(mixed $value): bool
    {
        return is_string($value) && '' !== $value;
    }

    public function getDefaultValue(): string
    {
        return 'sts_gaming_group_kafka';
    }
}
