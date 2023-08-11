<?php

declare(strict_types=1);

namespace Evirma\Bundle\KafkaBundle\Decoder;

use Evirma\Bundle\KafkaBundle\Configuration\ResolvedConfiguration;
use Evirma\Bundle\KafkaBundle\Contract\DecoderInterface;

class PlainDecoder implements DecoderInterface
{
    public function decode(ResolvedConfiguration $configuration, string $message): string
    {
        return $message;
    }
}
