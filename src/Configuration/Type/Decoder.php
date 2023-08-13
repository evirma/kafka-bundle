<?php

declare(strict_types=1);

namespace Evirma\Bundle\KafkaBundle\Configuration\Type;

use Evirma\Bundle\KafkaBundle\Configuration\Traits\ObjectConfigurationTrait;
use Evirma\Bundle\KafkaBundle\Contract\ConsumerConfigurationInterface;
use Evirma\Bundle\KafkaBundle\Contract\DecoderInterface;
use Evirma\Bundle\KafkaBundle\Decoder\JsonDecoder;
use Evirma\Bundle\KafkaBundle\Decoder\PlainDecoder;
use Symfony\Component\Console\Input\InputOption;

class Decoder implements ConsumerConfigurationInterface
{
    use ObjectConfigurationTrait;

    public const NAME = 'decoder';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getMode(): int
    {
        return InputOption::VALUE_REQUIRED;
    }

    public function getDescription(): string
    {
        return sprintf(
            'Which decoder to use. Currently available %s. 
            You can also create custom Decoder by implementing %s.
            Default decoder %s',
            implode(', ', [JsonDecoder::class, PlainDecoder::class]),
            DecoderInterface::class,
            $this->getDefaultValue()
        );
    }

    public function getDefaultValue(): string
    {
        return JsonDecoder::class;
    }

    protected function getInterface(): string
    {
        return DecoderInterface::class;
    }
}
