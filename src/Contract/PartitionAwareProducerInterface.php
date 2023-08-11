<?php

declare(strict_types=1);

namespace Evirma\Bundle\KafkaBundle\Contract;


use Evirma\Bundle\KafkaBundle\Configuration\ResolvedConfiguration;

interface PartitionAwareProducerInterface extends ProducerInterface
{
    public function getPartition(mixed $data, ResolvedConfiguration $configuration): int;
}
