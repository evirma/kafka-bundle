<?php

declare(strict_types=1);

namespace Evirma\Bundle\KafkaBundle\Configuration\Type;

use Evirma\Bundle\KafkaBundle\Configuration\Traits\ObjectConfigurationTrait;
use Evirma\Bundle\KafkaBundle\Contract\ConsumerConfigurationInterface;
use Evirma\Bundle\KafkaBundle\Contract\ValidatorInterface;
use Evirma\Bundle\KafkaBundle\Validator\PlainValidator;
use Symfony\Component\Console\Input\InputOption;

class Validators implements ConsumerConfigurationInterface
{
    use ObjectConfigurationTrait;

    public const NAME = 'validators';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getMode(): int
    {
        return InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY;
    }

    public function getDescription(): string
    {
        return sprintf(
            <<<EOT
            'Which validators to use after/before payload has been denormalized. Must implement %s.
            Defaults to %s which returns true by default.',
            EOT,
            ValidatorInterface::class,
            PlainValidator::class
        );
    }

    public function getDefaultValue(): array
    {
        return [PlainValidator::class];
    }

    protected function getInterface(): string
    {
        return ValidatorInterface::class;
    }
}
