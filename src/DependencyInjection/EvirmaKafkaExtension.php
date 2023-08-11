<?php

declare(strict_types=1);

namespace Evirma\Bundle\KafkaBundle\DependencyInjection;


use Evirma\Bundle\KafkaBundle\Configuration\Contract\ConfigurationInterface;
use Evirma\Bundle\KafkaBundle\Contract\ConsumerInterface;
use Evirma\Bundle\KafkaBundle\Contract\DecoderInterface;
use Evirma\Bundle\KafkaBundle\Contract\DenormalizerInterface;
use Evirma\Bundle\KafkaBundle\Contract\ProducerInterface;
use Evirma\Bundle\KafkaBundle\Contract\ValidatorInterface;
use Symfony\Component\Config\Definition\Exception\InvalidDefinitionException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class EvirmaKafkaExtension extends ConfigurableExtension implements CompilerPassInterface
{
    private const XML_CONFIGS = [
        'rd_kafka',
        'consumers',
//        'commands',
        'configurations',
        'configuration_types',
        'decoders',
        'producers',
        'denormalizers',
        'validators',
    ];

    /**
     * @throws \Exception
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/Resources/config'));
        foreach (self::XML_CONFIGS as $xmlFile) {
            $loader->load($xmlFile.'.xml');
        }

        $container->registerForAutoconfiguration(ConsumerInterface::class)
            ->addTag('evirma_kafka.kafka.consumer');

        $container->registerForAutoconfiguration(ProducerInterface::class)
            ->addTag('evirma_kafka.kafka.producer');

        $container->registerForAutoconfiguration(ConfigurationInterface::class)
            ->addTag('evirma_kafka.configuration.type');

        $container->registerForAutoconfiguration(DecoderInterface::class)
            ->addTag('evirma_kafka.decoder');

        $container->registerForAutoconfiguration(DenormalizerInterface::class)
            ->addTag('evirma_kafka.denormalizer');

        $container->registerForAutoconfiguration(ValidatorInterface::class)
            ->addTag('evirma_kafka.validator');

        $configurationResolver = $container->getDefinition('evirma_kafka.configuration.configuration_resolver');
        $configurationResolver->setArgument(1, $mergedConfig);
    }

    public function process(ContainerBuilder $container): void
    {
        $this->addConsumersAndProvider($container);
        $this->addProducersAndProvider($container);
        $this->addConfigurations($container);
    }

    private function addConsumersAndProvider(ContainerBuilder $container): void
    {
        $providerId = 'evirma_kafka.client.consumer.consumer_provider';
        if (!$container->has($providerId)) {
            throw new InvalidDefinitionException(
                sprintf('Unable to find any consumer provider. Looking for service id %s', $providerId)
            );
        }

        $consumerProvider = $container->findDefinition($providerId);
        $consumers = $container->findTaggedServiceIds('evirma_kafka.kafka.consumer');
        foreach ($consumers as $id => $tags) {
            $consumerProvider->addMethodCall('addConsumer', [new Reference($id)]);
        }
    }

    private function addProducersAndProvider(ContainerBuilder $container): void
    {
        $providerId = 'evirma_kafka.client.producer.producer_provider';
        if (!$container->has($providerId)) {
            throw new InvalidDefinitionException(
                sprintf('Unable to find any producer provider. Looking for service id %s', $providerId)
            );
        }

        $producerProvider = $container->findDefinition($providerId);
        $producers = $container->findTaggedServiceIds('evirma_kafka.kafka.producer');
        foreach ($producers as $id => $tags) {
            $producerProvider->addMethodCall('addProducer', [new Reference($id)]);
        }
    }

    private function addConfigurations(ContainerBuilder $container): void
    {
        $configurationsId = 'evirma_kafka.configuration.raw_configuration';
        if (!$container->has($configurationsId)) {
            throw new InvalidDefinitionException(
                sprintf('Unable to find configurations class. Looking for service id %s', $configurationsId)
            );
        }

        $configurations = $container->findDefinition($configurationsId);
        $configurationTypes = $container->findTaggedServiceIds('evirma_kafka.configuration.type');
        foreach ($configurationTypes as $id => $tags) {
            $configurations->addMethodCall('addConfiguration', [new Reference($id)]);
        }
    }
}
