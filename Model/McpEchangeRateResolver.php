<?php

namespace PayU\PaymentGateway\Model;

use PayU\PaymentGateway\Api\PayUMcpExchangeRateResolverInterface;
use PayU\PaymentGateway\Api\PayUGetMultiCurrencyPricingInterface;

/**
 * Class OrderPaymentResolver
 * @package PayU\PaymentGateway\Api
 */
class McpEchangeRateResolver implements PayUMcpExchangeRateResolverInterface
{
    /**
     * @var PayUGetMultiCurrencyPricingInterface
     */
    private $currencyPricing;

    /**
     * PayUMcpExchangeRateResolverInterface constructor.
     *
     * @param PayUGetMultiCurrencyPricingInterface $currencyPricing
     */
    public function __construct(PayUGetMultiCurrencyPricingInterface $currencyPricing)
    {
        $this->currencyPricing = $currencyPricing;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve($currentCurrency, $storeCurrency)
    {
        $rates = $this->currencyPricing->execute();
        if (isset($rates->currencyPairs)) {
            foreach ($rates->currencyPairs as $currencyPair) {
                if ($currencyPair->baseCurrency === $currentCurrency &&
                    $currencyPair->termCurrency === $storeCurrency) {
                    return $currencyPair->exchangeRate;
                }
            }
        }

        return null;
    }

}
