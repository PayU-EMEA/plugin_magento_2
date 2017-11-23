<?php

namespace PayU\PaymentGateway\Api;

use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Model\Order;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * Interface OrderPaymentResolverInterface
 * @package PayU\PaymentGateway\Api
 */
interface OrderPaymentResolverInterface
{
    /**
     * Get Real Last Payment by IncrementId
     *
     * @param Order|OrderInterface $order
     *
     * @return Payment
     */
    public function getLast($order);

    /**
     * @param string $txnId
     *
     * @return Payment
     */
    public function getByTransactionTxnId($txnId);
}
