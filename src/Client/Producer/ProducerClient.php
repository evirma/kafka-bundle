<?php

declare(strict_types=1);

namespace Evirma\Bundle\KafkaBundle\Client\Producer;

use Evirma\Bundle\KafkaBundle\Configuration\ConfigurationResolver;
use Evirma\Bundle\KafkaBundle\Configuration\ResolvedConfiguration;
use Evirma\Bundle\KafkaBundle\Configuration\Type\ProducerPartition;
use Evirma\Bundle\KafkaBundle\Configuration\Type\ProducerTopic;
use Evirma\Bundle\KafkaBundle\Contract\PartitionAwareProducerInterface;
use Evirma\Bundle\KafkaBundle\Contract\ProducerInterface;
use Evirma\Bundle\KafkaBundle\Rdkafka\KafkaConfiguration;
use RdKafka\Producer;

class ProducerClient
{
    private int $maxFlushRetries = 10;
    private int $flushTimeoutMs = 10000;
    private int $pollingBatch = 25000;
    private int $pollingTimeoutMs = 0;
    private array $rdKafkaProducers;

    private ?Producer $lastCalledProducer = null;

    public function __construct(
        private readonly ProducerProvider $producerProvider,
        private readonly KafkaConfiguration $kafkaConfigurationFactory,
        private readonly ConfigurationResolver $configurationResolver
    ) {
    }

    public function produce(mixed $data): self
    {
        $producer = $this->producerProvider->provide($data);
        $rdKafkaConfig = $this->kafkaConfigurationFactory->create($producer);

        $producerClass = get_class($producer);
        if (!isset($this->rdKafkaProducers[$producerClass])) {
            $this->rdKafkaProducers[$producerClass] = new Producer($rdKafkaConfig);
        }

        $this->lastCalledProducer = $this->rdKafkaProducers[$producerClass];
        $configuration = $this->configurationResolver->resolve($producer);

        $topic = $this->lastCalledProducer->newTopic($configuration->getValue(ProducerTopic::NAME));

        $message = $producer->produce($data);
        $topic->produce(
            $this->getPartition($data, $producer, $configuration),
            0,
            $message->getPayload(),
            $message->getKey()
        );

        if ($this->lastCalledProducer->getOutQLen() % $this->pollingBatch === 0) {
            while ($this->lastCalledProducer->getOutQLen() > 0) {
                $this->lastCalledProducer->poll($this->pollingTimeoutMs);
            }
        }

        return $this;
    }

    public function setMaxFlushRetries(int $maxFlushRetries): self
    {
        $this->maxFlushRetries = $maxFlushRetries;

        return $this;
    }

    public function setFlushTimeoutMs(int $flushTimeoutMs): self
    {
        $this->flushTimeoutMs = $flushTimeoutMs;

        return $this;
    }

    public function setPollingBatch(int $pollingBatch): self
    {
        $this->pollingBatch = $pollingBatch;

        return $this;
    }

    public function setPollingTimeoutMs(int $pollingTimeoutMs): self
    {
        $this->pollingTimeoutMs = $pollingTimeoutMs;

        return $this;
    }

    public function flush(): void
    {
        if (!$this->lastCalledProducer) {
            throw new \RuntimeException('You have to call `produce` method first to be able to flush.');
        }

        $result = RD_KAFKA_RESP_ERR_NO_ERROR;
        for ($flushRetries = 0; $flushRetries < $this->maxFlushRetries; $flushRetries++) {
            $result = $this->lastCalledProducer->flush($this->flushTimeoutMs);
            if (RD_KAFKA_RESP_ERR_NO_ERROR === $result) {
                break;
            }
        }

        if (RD_KAFKA_RESP_ERR_NO_ERROR !== $result) {
            throw new \RuntimeException('Unable to flush, messages might be lost.');
        }
    }

    private function getPartition(mixed $data, ProducerInterface $producer, ResolvedConfiguration $configuration): int
    {
        if (!$producer instanceof PartitionAwareProducerInterface) {
            return $configuration->getValue(ProducerPartition::NAME);
        }

        return $producer->getPartition($data, $configuration);
    }
}
