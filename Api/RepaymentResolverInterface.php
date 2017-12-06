<?php

namespace PayU\PaymentGateway\Api;

/**
 * Interface RepaymentResolverInterface
 * @package PayU\PaymentGateway\Api
 */
interface RepaymentResolverInterface
{
    /**
     * Is order can be repay
     *
     * @param int $orderId
     *
     * @return bool
     */
    public function isRepayment($orderId);
}
