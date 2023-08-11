<?php

declare(strict_types=1);

namespace Evirma\Bundle\KafkaBundle\Contract;

use Evirma\Bundle\KafkaBundle\Client\Consumer\Message;
use Evirma\Bundle\KafkaBundle\Rdkafka\Context;

interface ConsumerInterface extends ClientInterface
{
    public function consume(Message $message, Context $context): mixed;
    public function handleException(\Exception $exception, Context $context): mixed;
    public function getName(): string;
}
