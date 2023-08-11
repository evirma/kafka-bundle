<?php

declare(strict_types=1);

namespace Evirma\Bundle\KafkaBundle\Client\Producer;

use Evirma\Bundle\KafkaBundle\Contract\ProducerInterface;
use Evirma\Bundle\KafkaBundle\Exception\InvalidProducerException;

class ProducerProvider
{
    /**
     * @var array<ProducerInterface>
     */
    protected array $producers = [];

    public function addProducer(ProducerInterface $producer): self
    {
        $this->producers[] = $producer;

        return $this;
    }

    public function provide(mixed $data): ProducerInterface
    {
        $producers = [];

        foreach ($this->producers as $producer) {
            if ($producer->supports($data)) {
                $producers[] = $producer;
            }
        }

        if (count($producers) > 1) {
            throw new InvalidProducerException('Multiple producers found');
        }

        if (!$producers) {
            throw new InvalidProducerException('There is no matching producer.');
        }

        return $producers[0];
    }

    /**
     * @return array<ProducerInterface>
     */
    public function getProducers(): array
    {
        return $this->producers;
    }
}
