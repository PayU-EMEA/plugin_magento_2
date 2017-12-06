<?php

namespace PayU\PaymentGateway\Api;

use Magento\Payment\Gateway\Command\CommandException;

/**
 * Interface WaitingOrderPaymentInterface
 */
interface WaitingOrderPaymentInterface
{
    /**
     * Payment status history key
     */
    const PAYU_HISTORY_STATUS = 'payUStatus';

    /**
     * Set order status by status from PayU REST API (REJECTED, PAYMENT_REWIEV)
     *
     * @param string $txnId
     * @param string $payUStatus
     *
     * @return void
     * @throws CommandException
     */
    public function execute($txnId, $payUStatus);
}
