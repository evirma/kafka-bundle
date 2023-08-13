<?php

declare(strict_types=1);

namespace Evirma\Bundle\KafkaBundle\Consumer;

use Evirma\Bundle\KafkaBundle\Configuration\ResolvedConfiguration;
use Evirma\Bundle\KafkaBundle\Configuration\Type\Decoder;
use Evirma\Bundle\KafkaBundle\Configuration\Type\Denormalizer;
use Evirma\Bundle\KafkaBundle\Contract\DecoderInterface;
use Evirma\Bundle\KafkaBundle\Contract\DenormalizerInterface;
use Evirma\Bundle\KafkaBundle\Validator\Validator;
use RdKafka\Message as RdKafkaMessage;

class MessageFactory
{
    /**
     * @var array<DecoderInterface>
     */
    private array $decoders;

    /**
     * @var array<DenormalizerInterface>
     */
    private array $denormalizers;

    private Validator $validator;

    public function __construct(iterable $decoders, iterable $denormalizers, Validator $validator)
    {
        foreach ($decoders as $decoder) {
            $this->decoders[get_class($decoder)] = $decoder;
        }
        foreach ($denormalizers as $denormalizer) {
            $this->denormalizers[get_class($denormalizer)] = $denormalizer;
        }

        $this->validator = $validator;
    }

    public function create(RdKafkaMessage $rdKafkaMessage, ResolvedConfiguration $configuration): Message
    {
        $requiredDecoder = $configuration->getValue(Decoder::NAME);
        $decoded = $this->decoders[$requiredDecoder]->decode($configuration, $rdKafkaMessage->payload);

        $this->validator->validate($configuration, $decoded, Validator::PRE_DENORMALIZE_TYPE);

        $requiredDenormalizer = $configuration->getValue(Denormalizer::NAME);
        $denormalized = $this->denormalizers[$requiredDenormalizer]->denormalize($decoded);

        $this->validator->validate($configuration, $denormalized, Validator::POST_DENORMALIZE_TYPE);

        return new Message(
            $rdKafkaMessage->topic_name,
            $rdKafkaMessage->partition,
            $rdKafkaMessage->payload,
            $rdKafkaMessage->offset,
            $denormalized,
            $rdKafkaMessage->key
        );
    }
}
