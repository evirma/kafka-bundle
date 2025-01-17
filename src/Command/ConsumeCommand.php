<?php

declare(strict_types=1);

namespace Evirma\Bundle\KafkaBundle\Command;

use Evirma\Bundle\KafkaBundle\Configuration\ConfigurationResolver;
use Evirma\Bundle\KafkaBundle\Configuration\RawConfiguration;
use Evirma\Bundle\KafkaBundle\Consumer\ConsumerClient;
use Evirma\Bundle\KafkaBundle\Consumer\ConsumerProvider;
use Evirma\Bundle\KafkaBundle\Contract\ConsumerInterface;
use Evirma\Bundle\KafkaBundle\Traits\AddConfigurationsToCommandTrait;
use Evirma\Bundle\KafkaBundle\Traits\DescribeTrait;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('kafka:consumers:consume', 'tarts consuming messages from kafka')]
class ConsumeCommand extends Command
{
    use AddConfigurationsToCommandTrait;
    use DescribeTrait;

    public function __construct(
        private readonly RawConfiguration $rawConfiguration,
        private readonly ConsumerProvider $consumerProvider,
        private readonly ConsumerClient $consumerClient,
        private readonly ConfigurationResolver $configurationResolver
    ) {

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('kafka:consumers:consume')
            ->setDescription(
            sprintf(
                'Starts consuming messages from kafka using class implementing %s.',
                ConsumerInterface::class
            )
        )
            ->addArgument('name', InputArgument::REQUIRED, 'Name of the registered consumer.')
            ->addOption('describe', null, InputOption::VALUE_NONE, 'Describes consumer');

        $this->addConfigurations($this->rawConfiguration);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $consumer = $this->consumerProvider->provide($input->getArgument('name'));

        if ($input->getOption('describe')) {
            $this->describe($this->configurationResolver->resolve($consumer, $input), $output, $consumer);

            return 0;
        }

        $this->consumerClient->consume($consumer, $input);

        return 0;
    }
}
