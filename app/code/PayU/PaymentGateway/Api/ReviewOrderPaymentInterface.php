<?php

namespace PayU\PaymentGateway\Api;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Interface ReviewOrderPaymentInterface
 */
interface ReviewOrderPaymentInterface
{
    /**
     * Review order, provide action for accept and deny payment
     *
     * @param OrderInterface $order
     * @param string $action
     *
     * @return void
     * @throws LocalizedException
     */
    public function execute(OrderInterface $order, $action);
}
