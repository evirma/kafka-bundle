<?php

declare(strict_types=1);

namespace Evirma\Bundle\KafkaBundle\Traits;

use Evirma\Bundle\KafkaBundle\Configuration\Type\EnableAutoCommit;
use Evirma\Bundle\KafkaBundle\Exception\InvalidConfigurationException;
use Evirma\Bundle\KafkaBundle\Rdkafka\Context;
use RdKafka\Exception;

trait CommitOffsetTrait
{
    /**
     * @throws Exception
     */
    public function commitOffset(Context $context, bool $async = false): bool
    {
        if ($context->getValue(EnableAutoCommit::NAME) === 'true') {
            throw new InvalidConfigurationException(sprintf(
                'Unable to manually commit offset when %s configuration is set to `true`.',
                EnableAutoCommit::NAME
            ));
        }

        if ($async) {
            $context->getRdKafkaConsumer()->commitAsync($context->getRdKafkaMessage());
        } else {
            $context->getRdKafkaConsumer()->commit($context->getRdKafkaMessage());
        }

        return true;
    }
}
