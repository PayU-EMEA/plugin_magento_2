<?php

namespace PayU\PaymentGateway\Observer;

use Magento\Framework\DB\Transaction;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote;
use PayU\PaymentGateway\Api\PayUConfigInterface;
use PayU\PaymentGateway\Api\PayUMcpExchangeRateResolverInterface;

/**
 * Class McpCollectTotals
 * @package PayU\PaymentGateway\Observer
 */
class McpCollectTotals implements ObserverInterface
{
    /**
     * @var PayUMcpExchangeRateResolverInterface
     */
    private $exchangeRateResolver;

    /**
     * @var Transaction
     */
    private $transaction;

    /**
     * @var PayUConfigInterface
     */
    private $payUConfig;

    /**
     * McpCollectTotals constructor.
     *
     * @param PayUMcpExchangeRateResolverInterface $exchangeRateResolver
     * @param Transaction $transaction
     * @param PayUConfigInterface $payUConfig
     */
    public function __construct(
        PayUMcpExchangeRateResolverInterface $exchangeRateResolver,
        Transaction $transaction,
        PayUConfigInterface $payUConfig
    ) {
        $this->exchangeRateResolver = $exchangeRateResolver;
        $this->transaction = $transaction;
        $this->payUConfig = $payUConfig;
    }

    /**
     * @param Observer $observer
     *
     * @return $this
     */
    public function execute(Observer $observer)
    {
        if (!$this->payUConfig->isCrediCardCurrencyRates()) {
            return $this;
        }
        /** @var Quote $quote */
        $quote = $observer->getData('quote');
        $exchangeRate = $this->exchangeRateResolver->resolve(
            $quote->getQuoteCurrencyCode(),
            $quote->getStoreCurrencyCode()
        );
        if ($exchangeRate === null) {
            return $this;
        }
        $shippingAddress = $quote->getShippingAddress();
        $billingAddress = $quote->getBillingAddress();
        $fixedTotal = round(
            ($quote->getBaseSubtotal() + $shippingAddress->getBaseShippingAmount()) / $exchangeRate,
            2
        );
        $quote->setGrandTotal($fixedTotal);
        $shippingAddress->setGrandTotal($fixedTotal);
        $billingAddress->setGrandTotal($fixedTotal);
        $this->transaction->addObject($shippingAddress);
        $this->transaction->addObject($billingAddress);
        $this->transaction->addObject($quote);
        $this->transaction->save();

        return $this;
    }
}
