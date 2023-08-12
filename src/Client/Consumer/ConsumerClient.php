<?php

declare(strict_types=1);

namespace Evirma\Bundle\KafkaBundle\Client\Consumer;

use Evirma\Bundle\KafkaBundle\Client\Consumer\Exception\NullMessageException;
use Evirma\Bundle\KafkaBundle\Client\Consumer\Exception\RecoverableMessageException;
use Evirma\Bundle\KafkaBundle\Client\Consumer\Factory\MessageFactory;
use Evirma\Bundle\KafkaBundle\Configuration\ConfigurationResolver;
use Evirma\Bundle\KafkaBundle\Configuration\Type\EnableAutoCommit;
use Evirma\Bundle\KafkaBundle\Configuration\Type\MaxRetries;
use Evirma\Bundle\KafkaBundle\Configuration\Type\MaxRetryDelay;
use Evirma\Bundle\KafkaBundle\Configuration\Type\RetryDelay;
use Evirma\Bundle\KafkaBundle\Configuration\Type\RetryMultiplier;
use Evirma\Bundle\KafkaBundle\Configuration\Type\Timeout;
use Evirma\Bundle\KafkaBundle\Configuration\Type\Topics;
use Evirma\Bundle\KafkaBundle\Contract\ConsumerInterface;
use Evirma\Bundle\KafkaBundle\Event\PostMessageConsumedEvent;
use Evirma\Bundle\KafkaBundle\Event\PreMessageConsumedEvent;
use Evirma\Bundle\KafkaBundle\Exception\ValidationException;
use Evirma\Bundle\KafkaBundle\Rdkafka\Context;
use Evirma\Bundle\KafkaBundle\Rdkafka\KafkaConfiguration;
use RdKafka\Exception;
use RdKafka\KafkaConsumer as RdKafkaConsumer;
use RdKafka\Message as RdKafkaMessage;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ConsumerClient
{
//    use CheckForRdKafkaExtensionTrait;

    private int $consumedMessages = 0;
    private float $consumptionTimeMs = 0;

    public function __construct(
        private readonly KafkaConfiguration $kafkaConfigurationFactory,
        private readonly MessageFactory $messageFactory,
        private readonly ConfigurationResolver $configurationResolver,
        private readonly ?EventDispatcherInterface $dispatcher
    ) {
    }

    /**
     * @throws Exception
     */
    public function consume(ConsumerInterface $consumer, ?InputInterface $input = null): void
    {
        //$this->isKafkaExtensionLoaded();

        $configuration = $this->configurationResolver->resolve($consumer, $input);

        $timeout = $configuration->getValue(Timeout::NAME);
        $maxRetries = $configuration->getValue(MaxRetries::NAME);
        $retryDelay = $configuration->getValue(RetryDelay::NAME);
        $maxRetryDelay = $configuration->getValue(MaxRetryDelay::NAME);
        $retryMultiplier = $configuration->getValue(RetryMultiplier::NAME);
        $topics = $configuration->getValue(Topics::NAME);
        $enableAutoCommit = $configuration->getValue(EnableAutoCommit::NAME);

        $rdKafkaConfig = $this->kafkaConfigurationFactory->create($consumer, $input);
        $rdKafkaConsumer = new RdKafkaConsumer($rdKafkaConfig);
        $rdKafkaConsumer->subscribe($topics);

        $consumptionStart = microtime(true);
        while (true) {
            $this->dispatch(PreMessageConsumedEvent::class, $consumer);
            $rdKafkaMessage = $rdKafkaConsumer->consume($timeout);

            try {
                $this->validateRdKafkaMessage($rdKafkaMessage);
            } catch (NullMessageException $exception) {
                $consumer->handleException(
                    $exception,
                    new Context($configuration, $rdKafkaConsumer, $rdKafkaMessage, 0)
                );

                $this->setConsumptionTime($consumptionStart);

                continue;
            }

            for ($retry = 0; $retry <= $maxRetries; ++$retry) {
                $context = new Context($configuration, $rdKafkaConsumer, $rdKafkaMessage, $retry);
                try {
                    $message = $this->messageFactory->create($rdKafkaMessage, $configuration);
                    $consumer->consume($message, $context);
                } catch (ValidationException | RecoverableMessageException $exception) {
                    $consumer->handleException($exception, $context);

                    if ($exception instanceof ValidationException) {
                        if ($enableAutoCommit === 'false') {
                            $rdKafkaConsumer->commit($rdKafkaMessage);
                        }

                        break;
                    } else {
                        if ($retry !== $maxRetries) {
                            $retryDelay *= $retryMultiplier;
                            if ($retryDelay > $maxRetryDelay) {
                                $retryDelay = $maxRetryDelay;
                            }
                            usleep($retryDelay * 1000);
                        }

                        continue;
                    }
                }

                break;
            }

            $retryDelay = $configuration->getValue(RetryDelay::NAME);

            $this->increaseConsumedMessages();
            $this->setConsumptionTime($consumptionStart);

            $this->dispatch(PostMessageConsumedEvent::class, $consumer);
        }
    }

    private function setConsumptionTime(float $consumptionStart): void
    {
        $this->consumptionTimeMs = microtime(true) - $consumptionStart;
    }

    private function increaseConsumedMessages(): void
    {
        ++$this->consumedMessages;
    }

    private function validateRdKafkaMessage(?RdKafkaMessage $message): void
    {
        if (null === $message || RD_KAFKA_RESP_ERR__PARTITION_EOF === $message->err) {
            throw new NullMessageException('Currently, there are no more messages.');
        }

        if (RD_KAFKA_RESP_ERR__TIMED_OUT === $message->err) {
            throw new NullMessageException(
                'Kafka brokers have timed out or there are no messages. Unable to differentiate the reason.'
            );
        }

        if (null === $message->payload) {
            throw new NullMessageException('Null payload received in kafka message.');
        }
    }

    private function dispatch(string $eventClass, ConsumerInterface $consumer): void
    {
        if (!$this->dispatcher) {
            return;
        }

        $event = match ($eventClass) {
            PostMessageConsumedEvent::class => new PostMessageConsumedEvent($this->consumedMessages, $this->consumptionTimeMs),
            PreMessageConsumedEvent::class => new PreMessageConsumedEvent($this->consumedMessages, $this->consumptionTimeMs),
            default => throw new \RuntimeException(sprintf('Event class %s does not exist', $eventClass)),
        };

        $this->dispatcher->dispatch($event, $event::getEventName($consumer->getName()));
        $this->dispatcher->dispatch($event);
    }
}
