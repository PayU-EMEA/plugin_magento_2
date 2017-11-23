<?php

namespace PayU\PaymentGateway\Model\Currency\Import;

use Magento\Directory\Model\Currency\Import\AbstractImport;
use Magento\Directory\Model\CurrencyFactory;
use PayU\PaymentGateway\Api\PayUGetMultiCurrencyPricingInterface;

/**
 * Class MultiCurrencyPricing
 * @package PayU\PaymentGateway\Model\Currency\Import
 */
class MultiCurrencyPricing extends AbstractImport
{
    /**
     * @var PayUGetMultiCurrencyPricingInterface
     */
    private $currencyPricing;

    /**
     * MultiCurrencyPricing constructor.
     *
     * @param CurrencyFactory $currencyFactory
     * @param PayUGetMultiCurrencyPricingInterface $currencyPricing
     */
    public function __construct(CurrencyFactory $currencyFactory, PayUGetMultiCurrencyPricingInterface $currencyPricing)
    {
        $this->currencyPricing = $currencyPricing;
        parent::__construct($currencyFactory);
    }

    /**
     * {@inheritdoc}
     */
    protected function _convert($currencyFrom, $currencyTo)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function fetchRates()
    {
        return $this->getCurrentCurrencyPair();
    }

    /**
     * Get current currency rates
     *
     * @return array
     */
    private function getCurrentCurrencyPair()
    {
        $result = [];
        $currencies = $this->_getCurrencyCodes();
        $defaultCurrencies = $this->_getDefaultCurrencyCodes();
        $rates = $this->currencyPricing->execute();
        if (isset($rates->currencyPairs)) {
            foreach ($rates->currencyPairs as $currencyPair) {
                if (in_array($currencyPair->baseCurrency, $currencies) &&
                    $currencyPair->termCurrency === $defaultCurrencies[0]) {
                    $result[$currencyPair->baseCurrency] = 1 / $currencyPair->exchangeRate;
                }
            }
            $result[$defaultCurrencies[0]] = 1;
        }
        ksort($result);

        return [$defaultCurrencies[0] => $result];
    }
}
