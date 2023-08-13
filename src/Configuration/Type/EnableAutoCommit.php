<?php

declare(strict_types=1);

namespace Evirma\Bundle\KafkaBundle\Configuration\Type;

use Evirma\Bundle\KafkaBundle\Configuration\Traits\BooleanConfigurationTrait;
use Evirma\Bundle\KafkaBundle\Contract\ConsumerConfigurationInterface;
use Evirma\Bundle\KafkaBundle\Contract\KafkaConfigurationInterface;

class EnableAutoCommit implements KafkaConfigurationInterface, ConsumerConfigurationInterface
{
    use BooleanConfigurationTrait;

    public const NAME = 'enable_auto_commit';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getKafkaProperty(): string
    {
        return 'enable.auto.commit';
    }

    public function getDescription(): string
    {
        return <<<EOT
        If true, periodically commit offset of the last message handed to the application. 
        This committed offset will be used when the process restarts to pick up where it left off. 
        If false, the application will have to call rd_kafka_offset_store() to store an offset (optional).
        Defaults to true. Must be passed as a string `true` or `false`
        EOT;
    }

    public function getDefaultValue(): string
    {
        return 'true';
    }
}
