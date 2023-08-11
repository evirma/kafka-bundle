<?php

declare(strict_types=1);

namespace Evirma\Bundle\KafkaBundle\Event;

class PreMessageConsumedEvent extends AbstractMessageConsumedEvent
{
    private const NAME = 'evirma_kafka.pre_message_consumed';

    public static function getEventName(string $consumerName): string
    {
        return self::NAME . '_' . $consumerName;
    }
}
