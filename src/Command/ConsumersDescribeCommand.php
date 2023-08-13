<?php

declare(strict_types=1);

namespace Evirma\Bundle\KafkaBundle\Command;

use Evirma\Bundle\KafkaBundle\Configuration\ConfigurationResolver;
use Evirma\Bundle\KafkaBundle\Consumer\ConsumerProvider;
use Evirma\Bundle\KafkaBundle\Traits\DescribeTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ConsumersDescribeCommand extends Command
{
    use DescribeTrait;

    protected static $defaultName = 'kafka:consumers:describe';

    private ConsumerProvider $consumerProvider;
    private ConfigurationResolver $configurationResolver;

    public function __construct(
        ConsumerProvider $consumerProvider,
        ConfigurationResolver $configurationResolver
    ) {
        $this->consumerProvider = $consumerProvider;
        $this->configurationResolver = $configurationResolver;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Show consumers configuration.')
            ->addOption('name', null, InputOption::VALUE_REQUIRED, 'Shows specific consumer configuration.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getOption('name');
        $consumers = $name ? [$this->consumerProvider->provide($name)] : $this->consumerProvider->getAll();

        foreach ($consumers as $consumer) {
            $this->describe($this->configurationResolver->resolve($consumer), $output, $consumer);
        }

        return 0;
    }
}
