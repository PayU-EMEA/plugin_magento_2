<?php

namespace PayU\PaymentGateway\Api;

/**
 * Interface PayUGetMultiCurrencyPricingInterface
 * @package PayU\PaymentGateway\Api
 */
interface PayUGetMultiCurrencyPricingInterface
{
    /**
     * Retrive currency rates from PayU REST API
     *
     * @return \stdClass
     * @throws \OpenPayU_Exception_ServerError
     */
    public function execute();
}
