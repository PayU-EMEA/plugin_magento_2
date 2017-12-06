<?php

namespace PayU\PaymentGateway\Api;

use Magento\Payment\Gateway\Command\CommandException;

/**
 * Interface AcceptOrderPaymentInterface
 */
interface AcceptOrderPaymentInterface
{
    /**
     * Accept order payment by capture payment, generate invoice and send email invoice email to customer
     *
     * @param string $txnId
     * @param float $amount
     * @param string|null $paymentId
     *
     * @return void
     * @throws CommandException
     */
    public function execute($txnId, $amount, $paymentId = null);
}
