<?php

declare(strict_types=1);

namespace Evirma\Bundle\KafkaBundle\Validator;

use Evirma\Bundle\KafkaBundle\Configuration\ResolvedConfiguration;
use Evirma\Bundle\KafkaBundle\Configuration\Type\Validators;
use Evirma\Bundle\KafkaBundle\Contract\ValidatorInterface;
use Evirma\Bundle\KafkaBundle\Exception\ValidationException;

class Validator
{
    public const PRE_DENORMALIZE_TYPE = 'pre_denormalize';
    public const POST_DENORMALIZE_TYPE = 'post_denormalize';

    /**
     * @var array<ValidatorInterface>
     */
    private array $preDenormalizeValidators = [];
    /**
     * @var array<ValidatorInterface>
     */
    private array $postDenormalizeValidators = [];

    /**
     * @param iterable<ValidatorInterface> $validators
     */
    public function __construct(iterable $validators)
    {
        foreach ($validators as $validator) {
            if ($validator->type() === self::PRE_DENORMALIZE_TYPE) {
                $this->preDenormalizeValidators[get_class($validator)] = $validator;
            }
            if ($validator->type() === self::POST_DENORMALIZE_TYPE) {
                $this->postDenormalizeValidators[get_class($validator)] = $validator;
            }
        }
    }

    public function validate(ResolvedConfiguration $configuration, mixed $data, string $type): bool
    {
        if ($type !== self::PRE_DENORMALIZE_TYPE && $type !== self::POST_DENORMALIZE_TYPE) {
            throw new \RuntimeException(sprintf(
                'Type must be either %s or %s.',
                self::PRE_DENORMALIZE_TYPE,
                self::POST_DENORMALIZE_TYPE
            ));
        }

        $validators = $type === self::PRE_DENORMALIZE_TYPE ?
            $this->preDenormalizeValidators :
            $this->postDenormalizeValidators;

        $requiredValidators = $configuration->getValue(Validators::NAME);
        foreach ($requiredValidators as $requiredValidator) {
            if (isset($validators[$requiredValidator]) && !$validators[$requiredValidator]->validate($data)) {
                throw new ValidationException(
                    $validators[$requiredValidator],
                    $validators[$requiredValidator]->failureReason($data),
                    $data,
                    sprintf('Validation not passed by %s', $requiredValidator)
                );
            }
        }

        return true;
    }
}
