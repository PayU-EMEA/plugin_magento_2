<?php

namespace PayU\PaymentGateway\Api;

/**
 * Interface CancelOrderPaymentInterface
 */
interface CancelOrderPaymentInterface
{
    /**
     * Cancel order payment by cancel method
     *
     * @param string $txnId
     * @param float $amount
     *
     * @return void
     */
    public function execute($txnId, $amount);
}
