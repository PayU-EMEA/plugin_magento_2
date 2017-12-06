<?php

namespace PayU\PaymentGateway\Gateway\Validator\Order;

use PayU\PaymentGateway\Gateway\Validator\AbstractResponseValidator;

/**
 * Class ResponseCreatePblValidator
 * @package PayU\PaymentGateway\Gateway\Validator
 */
class ResponseCreatePblValidator extends AbstractResponseValidator
{
    /**
     * {@inheritdoc}
     */
    protected function isSuccessfulTransaction()
    {
        return array_key_exists(static::VALIDATION_SUBJECT_STATUS, $this->response) &&
            isset($this->response[static::VALIDATION_SUBJECT_STATUS]->statusCode) &&
            $this->response[static::VALIDATION_SUBJECT_STATUS]->statusCode === \OpenPayU_Order::SUCCESS;
    }
}
