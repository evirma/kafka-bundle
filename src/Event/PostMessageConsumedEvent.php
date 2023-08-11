<?php

declare(strict_types=1);

namespace Evirma\Bundle\KafkaBundle\Event;

class PostMessageConsumedEvent extends AbstractMessageConsumedEvent
{
    private const NAME = 'evirma_kafka.post_message_consumed';

    public static function getEventName(string $consumerName): string
    {
        return self::NAME . '_' . $consumerName;
    }
}
