<?php

namespace PayU\PaymentGateway\Api;

use Magento\Sales\Api\Data\OrderInterface;

/**
 * Interface PayURepayOrderInterface
 * @package PayU\PaymentGateway\Api
 */
interface PayURepayOrderInterface
{
    /**
     * Reorder by PayU REST API.
     *
     * @param OrderInterface $order
     * @param string $method
     * @param string $payUMethodType
     * @param string $payUMethod
     * @param string $transactionId
     *
     * @return array
     */
    public function execute(OrderInterface $order, $method, $payUMethodType, $payUMethod, $transactionId);
}
