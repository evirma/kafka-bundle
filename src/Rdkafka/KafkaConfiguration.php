<?php

declare(strict_types=1);

namespace Evirma\Bundle\KafkaBundle\Rdkafka;

use Evirma\Bundle\KafkaBundle\Configuration\ConfigurationResolver;
use Evirma\Bundle\KafkaBundle\Configuration\ResolvedConfiguration;
use Evirma\Bundle\KafkaBundle\Contract\CallableInterface;
use Evirma\Bundle\KafkaBundle\Contract\ClientInterface;
use RdKafka\Conf;
use Symfony\Component\Console\Input\InputInterface;

class KafkaConfiguration
{
    private ConfigurationResolver $configurationResolver;

    public function __construct(ConfigurationResolver $configurationResolver)
    {
        $this->configurationResolver = $configurationResolver;
    }

    public function create(ClientInterface $client, ?InputInterface $input = null): Conf
    {
        $configuration = $this->configurationResolver->resolve($client, $input);
        $conf = new Conf();

        foreach ($configuration->getConfigurations(ResolvedConfiguration::KAFKA_TYPES) as $kafkaConfiguration) {
            $resolvedValue = $kafkaConfiguration['resolvedValue'];
            $value = is_array($resolvedValue) ? implode(',', $resolvedValue) : $resolvedValue;
            $conf->set(
                $kafkaConfiguration['configuration']->getKafkaProperty(),
                $value
            );
        }

        if ($client instanceof CallableInterface) {
            $callbacks = $client->callbacks();
            foreach ($callbacks as $name => $callback) {
                $conf->{$name}($callback);
            }
        }

        return $conf;
    }

}
