<?php

declare(strict_types=1);

namespace Evirma\Bundle\KafkaBundle\Configuration\Type;

use Evirma\Bundle\KafkaBundle\Contract\ConsumerConfigurationInterface;
use Symfony\Component\Console\Input\InputOption;

class RetryDelay implements ConsumerConfigurationInterface
{
    public const NAME = 'retry_delay';

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
            'Delay (in ms) before message is consumed again after thrown exception. Defaults to %s',
            $this->getDefaultValue()
        );
    }

    public function isValueValid(mixed $value): bool
    {
        return is_int($value) && $value >= 0;
    }

    public function getDefaultValue(): int
    {
        return 200;
    }
}
