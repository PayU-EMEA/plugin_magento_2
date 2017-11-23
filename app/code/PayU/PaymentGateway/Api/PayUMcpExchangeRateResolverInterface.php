<?php

namespace PayU\PaymentGateway\Api;

/**
 * Interface OrderPaymentResolverInterface
 * @package PayU\PaymentGateway\Api
 */
interface PayUMcpExchangeRateResolverInterface
{
    /**
     * Get Ecxchange Rate store in DB from PayU MCP
     *
     * @param string $currentCurrency
     * @param string $storeCurrency
     *
     * @return float|null
     */
    public function resolve($currentCurrency, $storeCurrency);
}
