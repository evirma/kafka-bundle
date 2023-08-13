<?php

declare(strict_types=1);

namespace Evirma\Bundle\KafkaBundle\Configuration\Type;

use Evirma\Bundle\KafkaBundle\Configuration\Traits\BooleanConfigurationTrait;
use Evirma\Bundle\KafkaBundle\Contract\ConsumerConfigurationInterface;
use Evirma\Bundle\KafkaBundle\Contract\KafkaConfigurationInterface;

class EnableAutoOffsetStore implements KafkaConfigurationInterface, ConsumerConfigurationInterface
{
    use BooleanConfigurationTrait;

    public const NAME = 'enable_auto_offset_store';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getKafkaProperty(): string
    {
        return 'enable.auto.offset.store';
    }

    public function getDescription(): string
    {
        return <<<EOT
        Automatically store offset of last message provided to application. 
        The offset store is an in-memory store of the next offset to (auto-)commit for each partition. 
        Defaults to true. Must be passed as a string `true` or `false`
        EOT;
    }

    public function getDefaultValue(): string
    {
        return 'true';
    }
}
