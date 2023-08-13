<?php

declare(strict_types=1);

namespace Evirma\Bundle\KafkaBundle\Command;

use Evirma\Bundle\KafkaBundle\Command\Traits\DescribeTrait;
use Evirma\Bundle\KafkaBundle\Configuration\ConfigurationResolver;
use Evirma\Bundle\KafkaBundle\Producer\ProducerProvider;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProducersDescribeCommand extends Command
{
    use DescribeTrait;

    protected static $defaultName = 'kafka:producers:describe';

    private ProducerProvider $producerProvider;
    private ConfigurationResolver $configurationResolver;

    public function __construct(
        ProducerProvider $producerProvider,
        ConfigurationResolver $configurationResolver
    ) {
        $this->producerProvider = $producerProvider;
        $this->configurationResolver = $configurationResolver;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Show producers configuration.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $producers = $this->producerProvider->getProducers();

        foreach ($producers as $producer) {
            $this->describe($this->configurationResolver->resolve($producer), $output, $producer);
        }

        return 0;
    }
}
