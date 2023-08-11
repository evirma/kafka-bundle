<?php

declare(strict_types=1);

namespace Evirma\Bundle\KafkaBundle\Rdkafka;

use Evirma\Bundle\KafkaBundle\Configuration\ResolvedConfiguration;
use RdKafka\KafkaConsumer as RdKafkaConsumer;
use RdKafka\Message as RdKafkaMessage;

readonly class Context
{
    public function __construct(
        private ResolvedConfiguration $configuration,
        private RdKafkaConsumer $consumer,
        private RdKafkaMessage $message,
        private int $retryNo
    ) {
    }

    public function getValue(string $name): mixed
    {
        return $this->configuration->getValue($name);
    }

    public function getRdKafkaConsumer(): RdKafkaConsumer
    {
        return $this->consumer;
    }

    public function getRdKafkaMessage(): RdKafkaMessage
    {
        return $this->message;
    }

    public function getRetryNo(): int
    {
        return $this->retryNo;
    }
}
