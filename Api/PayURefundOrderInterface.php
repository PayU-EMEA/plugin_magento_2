<?php

namespace PayU\PaymentGateway\Api;

/**
 * Interface PayURefundOrderInterface
 * @package PayU\PaymentGateway\Api
 */
interface PayURefundOrderInterface
{
    /**
     * Refund order by PayU REST API. Refund can be partial when amount to refund not match total amount of order
     *
     * @param int $orderId
     * @param string $type
     * @param string $description
     * @param int|null $amount
     *
     * @return array
     * @throws \OpenPayU_Exception
     * @throws \OpenPayU_Exception_Network
     */
    public function execute($orderId, $type, $description = '', $amount = null);
}
