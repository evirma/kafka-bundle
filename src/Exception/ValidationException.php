<?php

declare(strict_types=1);

namespace Evirma\Bundle\KafkaBundle\Exception;


use Evirma\Bundle\KafkaBundle\Contract\ValidatorInterface;

class ValidationException extends \RuntimeException
{
    private ValidatorInterface $validator;
    private string $failedReason;
    private mixed $data;

    public function __construct(ValidatorInterface $validator, string $failedReason, mixed $data, string $message)
    {
        $this->validator = $validator;
        $this->failedReason = $failedReason;
        $this->data = $data;

        parent::__construct($message);
    }

    public function getFailedValidator(): ValidatorInterface
    {
        return $this->validator;
    }

    public function getFailedReason(): string
    {
        return $this->failedReason;
    }

    public function getData(): mixed
    {
        return $this->data;
    }
}
