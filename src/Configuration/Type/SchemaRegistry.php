<?php

declare(strict_types=1);

namespace Evirma\Bundle\KafkaBundle\Configuration\Type;

use Evirma\Bundle\KafkaBundle\Contract\ConsumerConfigurationInterface;
use Symfony\Component\Console\Input\InputOption;

class SchemaRegistry implements ConsumerConfigurationInterface
{
    public const NAME = 'schema_registry';

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
            'Schema registry url needed for decoding/encoding messages. Defaults to %s.',
            $this->getDefaultValue()
        );
    }

    public function isValueValid(mixed $value): bool
    {
        return is_string($value) && '' !== $value;
    }

    public function getDefaultValue(): string
    {
        return '127.0.0.1';
    }
}
