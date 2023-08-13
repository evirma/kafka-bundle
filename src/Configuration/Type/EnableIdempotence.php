<?php

declare(strict_types=1);

namespace Evirma\Bundle\KafkaBundle\Configuration\Type;

use Evirma\Bundle\KafkaBundle\Configuration\Traits\BooleanConfigurationTrait;
use Evirma\Bundle\KafkaBundle\Contract\ConsumerConfigurationInterface;
use Evirma\Bundle\KafkaBundle\Contract\KafkaConfigurationInterface;

class EnableIdempotence implements KafkaConfigurationInterface, ConsumerConfigurationInterface
{
    use BooleanConfigurationTrait;

    public const NAME = 'enable_idempotence';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getKafkaProperty(): string
    {
        return 'enable.idempotence';
    }

    public function getDescription(): string
    {
        return <<<EOT
        Defaults to true. Must be passed as a string `true` or `false`
        EOT;
    }

    public function getDefaultValue(): string
    {
        return 'true';
    }
}
