<?php

namespace PayU\PaymentGateway\Api;

/**
 * Interface PayUUpdateRefundStatusInterface
 * @package PayU\PaymentGateway\Api
 */
interface PayUUpdateRefundStatusInterface
{
    /**
     * Refund status finalized
     */
    const STATUS_FINALIZED = 'FINALIZED';

    /**
     * Refund status canceled
     */
    const STATUS_CANCELED = 'CANCELED';

    /**
     * Cancel refund
     *
     * @param string $extOrderId
     *
     * @return void
     */
    public function cancel($extOrderId);

    /**
     * Update refund status of order
     *
     * @param string $extOrderId
     *
     * @return void
     */
    public function addSuccessMessage($extOrderId);
}
