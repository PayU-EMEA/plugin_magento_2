<?php

namespace PayU\PaymentGateway\Gateway\Validator\Order;

use PayU\PaymentGateway\Gateway\Validator\AbstractResponseValidator;

/**
 * Class ResponseRefundValidator
 * @package PayU\PaymentGateway\Gateway\Validator\Order
 */
class ResponseRefundValidator extends AbstractResponseValidator
{
    /**
     * {@inheritdoc}
     */
    protected function isSuccessfulTransaction()
    {
        return array_key_exists(static::VALIDATION_SUBJECT_STATUS, $this->response) &&
            $this->response[static::VALIDATION_SUBJECT_STATUS] === \OpenPayU_Order::SUCCESS;
    }
}
