<?php

declare(strict_types=1);

namespace Evirma\Bundle\KafkaBundle\Configuration;

use Evirma\Bundle\KafkaBundle\Contract\ConfigurationInterface;
use Evirma\Bundle\KafkaBundle\Exception\InvalidConfigurationException;

class RawConfiguration
{
    /**
     * @var array<ConfigurationInterface>
     */
    private array $configurations = [];

    public function addConfiguration(ConfigurationInterface $configuration): self
    {
        $this->validateConfiguration($configuration);
        $this->configurations[$configuration->getName()] = $configuration;

        return $this;
    }

    /**
     * @return array<ConfigurationInterface>
     */
    public function getConfigurations(): array
    {
        return $this->configurations;
    }

    public function getConfigurationByName(string $name): ConfigurationInterface
    {
        return $this->configurations[$name];
    }

    private function validateConfiguration(ConfigurationInterface $configuration): void
    {
        if (array_key_exists($configuration->getName(), $this->configurations)) {
            throw new InvalidConfigurationException(
                sprintf(
                    'Configuration with name `%s` has already been registered',
                    $configuration->getName()
                )
            );
        }
    }
}
