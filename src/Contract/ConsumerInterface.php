<?php

declare(strict_types=1);

namespace Evirma\Bundle\KafkaBundle\Contract;

use Evirma\Bundle\KafkaBundle\Consumer\Message;
use Evirma\Bundle\KafkaBundle\Rdkafka\Context;

interface ConsumerInterface extends ClientInterface
{
    public function consume(Message $message, Context $context): void;
    public function handleException(\Exception $exception, Context $context): void;
    public function getName(): string;
}
