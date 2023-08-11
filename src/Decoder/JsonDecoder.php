<?php

declare(strict_types=1);

namespace Evirma\Bundle\KafkaBundle\Decoder;

use Evirma\Bundle\KafkaBundle\Configuration\ResolvedConfiguration;
use Evirma\Bundle\KafkaBundle\Contract\DecoderInterface;

class JsonDecoder implements DecoderInterface
{
    /**
     * @throws \JsonException
     */
    public function decode(ResolvedConfiguration $configuration, string $message): array
    {
        return json_decode($message, true, 512, JSON_THROW_ON_ERROR);
    }
}
