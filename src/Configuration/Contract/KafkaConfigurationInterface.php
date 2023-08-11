<?php

declare(strict_types=1);

namespace Evirma\Bundle\KafkaBundle\Configuration\Contract;

interface KafkaConfigurationInterface extends ConfigurationInterface
{
    public function getKafkaProperty(): string;
}
