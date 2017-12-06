<?php

namespace PayU\PaymentGateway\Api;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;

/**
 * Interface OrderByIncrementIdResolverInterface
 * @package PayU\PaymentGateway\Api
 */
interface OrderByExtOrderIdResolverInterface
{
    /**
     * Get order by ext order id from PayU REST API
     *
     * @param int $extOrderId
     *
     * @return OrderInterface|Order
     */
    public function resolve($extOrderId);
}
