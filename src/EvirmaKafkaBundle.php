<?php

declare(strict_types=1);

namespace Evirma\Bundle\KafkaBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class EvirmaKafkaBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
