<?php

declare(strict_types=1);

namespace Evirma\Bundle\KafkaBundle\Contract;

interface ConfigurationInterface
{
    public function getName(): string;
    public function getMode(): int;
    public function getDescription(): string;
    public function isValueValid(mixed $value): bool;
    public function getDefaultValue(): mixed;
}
