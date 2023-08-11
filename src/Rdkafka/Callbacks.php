<?php

declare(strict_types=1);

namespace Evirma\Bundle\KafkaBundle\Rdkafka;

use RdKafka\KafkaConsumer;
use RdKafka\Message;
use RdKafka\Producer;

/**
 * @method setErrorCb(KafkaConsumer $kafkaConsumer, int $error, array $partitions)
 * @method setLogCb(KafkaConsumer $kafkaConsumer, int $level, string $facility, string $message)
 * @method setConsumeCb(Message $message)
 * @method setDrMsgCb(Producer $kafkaProducer, Message $message)
 * @method setOffsetCommitCb(KafkaConsumer $kafkaConsumer, int $error, array $partitions)
 * @method setRebalanceCb(KafkaConsumer $kafkaConsumer, int $error, array $partitions)
 * @method setStatsCb($kafka, string $json, int $jsonLength) unable to check this callback
 */
class Callbacks
{
    public const ERROR_CALLBACK = 'setErrorCb';
    public const LOG_CALLBACK = 'setLogCb';
    public const CONSUME_CALLBACK = 'setConsumeCb';
    public const MESSAGE_DELIVERY_CALLBACK = 'setDrMsgCb';
    public const OFFSET_COMMIT_CALLBACK = 'setOffsetCommitCb';
    public const REBALANCE_CALLBACK = 'setRebalanceCb';
    public const STATISTICS_CALLBACK = 'setStatsCb';
}
