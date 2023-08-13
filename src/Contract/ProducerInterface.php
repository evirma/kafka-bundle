<?php

declare(strict_types=1);

namespace Evirma\Bundle\KafkaBundle\Contract;

use Evirma\Bundle\KafkaBundle\Producer\Message;

interface ProducerInterface extends ClientInterface
{
    public function produce(mixed $data): Message;
    public function supports(mixed $data): bool;
}
