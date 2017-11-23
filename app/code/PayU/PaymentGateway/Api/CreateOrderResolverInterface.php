<?php

namespace PayU\PaymentGateway\Api;

use Magento\Payment\Gateway\Data\OrderAdapterInterface;

/**
 * Interface CreateOrderResolverInterface
 * @package PayU\PaymentGateway\Api
 */
interface CreateOrderResolverInterface
{
    /**
     * OCR Buyer email field
     */
    const BUYER_EMAIL = 'email';

    /**
     * Get data for create order in PayU REST API
     *
     * @param OrderAdapterInterface $order
     * @param string $methodTypeCode
     * @param string $methodCode
     * @param null|float $totalDue
     * @param null|float $orderCurrencyCode
     * @param string $coninueUrl
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    public function resolve(
        OrderAdapterInterface $order,
        $methodTypeCode,
        $methodCode,
        $totalDue = null,
        $orderCurrencyCode = null,
        $coninueUrl = 'checkout/onepage/success'
    );
}
