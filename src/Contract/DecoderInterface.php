<?php

declare(strict_types=1);

namespace Evirma\Bundle\KafkaBundle\Contract;

use Evirma\Bundle\KafkaBundle\Configuration\ResolvedConfiguration;

interface DecoderInterface
{
    public function decode(ResolvedConfiguration $configuration, string $message): mixed;
}
