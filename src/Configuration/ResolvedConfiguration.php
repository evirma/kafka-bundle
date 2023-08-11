<?php

declare(strict_types=1);

namespace Evirma\Bundle\KafkaBundle\Configuration;

use Evirma\Bundle\KafkaBundle\Configuration\Contract\ConfigurationInterface;
use Evirma\Bundle\KafkaBundle\Configuration\Contract\ConsumerConfigurationInterface;
use Evirma\Bundle\KafkaBundle\Configuration\Contract\KafkaConfigurationInterface;
use Evirma\Bundle\KafkaBundle\Configuration\Contract\ProducerConfigurationInterface;
use Evirma\Bundle\KafkaBundle\Configuration\Exception\InvalidConfigurationType;

class ResolvedConfiguration
{
    public const ALL_TYPES = 'all';
    public const KAFKA_TYPES = 'kafka';
    public const CONSUMER_TYPES = 'consumer';
    public const PRODUCER_TYPES = 'producer';

    private array $configurations = [];

    public function addConfiguration(ConfigurationInterface $configuration, mixed $resolvedValue): self
    {
        $this->configurations[$configuration->getName()] = [
            'configuration' => $configuration,
            'resolvedValue' => $resolvedValue
        ];

        return $this;
    }

    /**
     * @param string $type
     * @return array
     */
    public function getConfigurations(string $type = self::ALL_TYPES): array
    {
        $interface = match ($type) {
            self::ALL_TYPES => ConfigurationInterface::class,
            self::KAFKA_TYPES => KafkaConfigurationInterface::class,
            self::CONSUMER_TYPES => ConsumerConfigurationInterface::class,
            self::PRODUCER_TYPES => ProducerConfigurationInterface::class,
            default => throw new InvalidConfigurationType(sprintf('Unknown configuration type %s', $type)),
        };

        $configurations = [];
        foreach ($this->configurations as $configuration) {
            if ($configuration['configuration'] instanceof $interface) {
                $configurations[] = $configuration;
            }
        }

        return $configurations;
    }

    public function getValue(string $name): mixed
    {
        return $this->configurations[$name]['resolvedValue'];
    }
}
