<?php

namespace PayU\PaymentGateway\Gateway\Validator\Order;

use PayU\PaymentGateway\Api\PayUCreateOrderInterface;
use PayU\PaymentGateway\Gateway\Validator\AbstractResponseValidator;

/**
 * Class ResponseCreateCardValidator
 * @package PayU\PaymentGateway\Gateway\Validator
 */
class ResponseCreateCardValidator extends AbstractResponseValidator
{
    /**
     * {@inheritdoc}
     */
    protected function isSuccessfulTransaction()
    {
        if ($this->hasStatusKey()) {
            return $this->response[static::VALIDATION_SUBJECT_STATUS]->statusCode === \OpenPayU_Order::SUCCESS ||
                $this->response[static::VALIDATION_SUBJECT_STATUS]->statusCode ===
                PayUCreateOrderInterface::WARNING_CONTINUE_CVV ||
                $this->response[static::VALIDATION_SUBJECT_STATUS]->statusCode ===
                PayUCreateOrderInterface::WARNING_CONTINUE_3_DS;
        }

        return false;
    }

    /**
     * Is response has status
     *
     * @return bool
     */
    private function hasStatusKey()
    {
        return array_key_exists(static::VALIDATION_SUBJECT_STATUS, $this->response) &&
            isset($this->response[static::VALIDATION_SUBJECT_STATUS]->statusCode);
    }
}
