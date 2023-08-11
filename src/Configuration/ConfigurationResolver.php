<?php

declare(strict_types=1);

namespace Evirma\Bundle\KafkaBundle\Configuration;

use Evirma\Bundle\KafkaBundle\Configuration\Contract\CastValueInterface;
use Evirma\Bundle\KafkaBundle\Configuration\Contract\ConfigurationInterface;
use Evirma\Bundle\KafkaBundle\Configuration\Exception\InvalidClientException;
use Evirma\Bundle\KafkaBundle\Configuration\Exception\InvalidConfigurationException;
use Evirma\Bundle\KafkaBundle\Contract\ClientInterface;
use Evirma\Bundle\KafkaBundle\Contract\ConsumerInterface;
use Evirma\Bundle\KafkaBundle\Contract\ProducerInterface;
use Symfony\Component\Console\Input\InputInterface;

class ConfigurationResolver
{
    private RawConfiguration $rawConfiguration;
    private array $yamlConfig;

    public function __construct(RawConfiguration $rawConfiguration, array $yamlConfig)
    {
        $this->rawConfiguration = $rawConfiguration;
        $this->yamlConfig = $yamlConfig;
    }

    public function resolve(string|ClientInterface $clientClass, ?InputInterface $input = null): ResolvedConfiguration
    {
        $configuration = new ResolvedConfiguration();

        foreach ($this->rawConfiguration->getConfigurations() as $rawConfiguration) {
            $resolvedValue = $this->getResolvedValue($rawConfiguration, $clientClass, $input);

            if ($rawConfiguration instanceof CastValueInterface) {
                $resolvedValue = $rawConfiguration->cast($resolvedValue);
            }

            $configuration->addConfiguration($rawConfiguration, $resolvedValue);
        }

        return $configuration;
    }

    /**
     * @param ConfigurationInterface $configuration
     * @param string|ClientInterface $clientClass
     * @param InputInterface|null $input
     * @return mixed
     */
    private function getResolvedValue(
        ConfigurationInterface $configuration,
        string|ClientInterface $clientClass,
        ?InputInterface $input
    ): mixed {
        $type = '';
        if (is_a($clientClass, ConsumerInterface::class, true)) {
            $type = 'consumers';
        }

        if (is_a($clientClass, ProducerInterface::class, true)) {
            $type = 'producers';
        }

        if (!$type) {
            throw new InvalidClientException(sprintf(
                'Object must implement %s or %s to properly resolve configuration.',
                ConsumerInterface::class,
                ProducerInterface::class
            ));
        }

        $name = $configuration->getName();
        if ($input && $input->getParameterOption('--' . $name) !== false) {
            $resolvedValue = $input->getOption($name);
            $this->validateResolvedValue($configuration, $resolvedValue);

            return $resolvedValue;
        }

        $clientClass = is_string($clientClass) ? $clientClass : get_class($clientClass);
        if ($this->shouldResolveInstance($clientClass, $type, $configuration)) {
            $resolvedValue = $this->yamlConfig[$type]['instances'][$clientClass][$name];
            $this->validateResolvedValue($configuration, $resolvedValue);

            return $resolvedValue;
        }

        $parentClass = $this->getParentClass($clientClass);
        if ($this->shouldResolveInstance($parentClass, $type, $configuration)) {
            $resolvedValue = $this->yamlConfig[$type]['instances'][$parentClass][$name];
            $this->validateResolvedValue($configuration, $resolvedValue);

            return $resolvedValue;
        }

        return $configuration->getDefaultValue();
    }

    private function validateResolvedValue(ConfigurationInterface $configuration, mixed $resolvedValue): void
    {
        if (!$configuration->isValueValid($resolvedValue)) {
            throw new InvalidConfigurationException(sprintf(
                'Invalid option passed for %s. Passed value `%s`. Configuration description: %s',
                $configuration->getName(),
                is_array($resolvedValue) ? implode(', ', $resolvedValue) : $resolvedValue,
                $configuration->getDescription()
            ));
        }
    }

    /**
     * @param string|ClientInterface $clientClass
     * @return string
     */
    private function getParentClass(string|ClientInterface $clientClass): string
    {
        $parentClass = get_parent_class($clientClass);

        return $parentClass === false ? '' : $parentClass;
    }

    private function shouldResolveInstance(string $class, string $type, ConfigurationInterface $configuration): bool
    {
        $name = $configuration->getName();

        return isset($this->yamlConfig[$type]['instances'][$class][$name]) &&
            $this->yamlConfig[$type]['instances'][$class][$name];
    }
}
