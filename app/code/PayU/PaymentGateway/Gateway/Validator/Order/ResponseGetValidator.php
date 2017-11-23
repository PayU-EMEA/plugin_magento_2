<?php

namespace PayU\PaymentGateway\Gateway\Validator\Order;

use PayU\PaymentGateway\Gateway\Validator\AbstractResponseValidator;

/**
 * Class ResponseGetValidator
 * @package PayU\PaymentGateway\Gateway\Validator\Order
 */
class ResponseGetValidator extends AbstractResponseValidator
{
    /**
     * {@inheritdoc}
     */
    protected function isSuccessfulTransaction()
    {
        return array_key_exists(static::VALIDATION_SUBJECT_STATUS, $this->response) &&
            $this->response[static::VALIDATION_SUBJECT_STATUS] === 'COMPLETED';
    }
}
