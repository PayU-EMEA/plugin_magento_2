<?php

namespace PayU\PaymentGateway\Api;

/**
 * Interface PayUGetCreditCardSecureFormConfigInterface
 * @package PayU\PaymentGateway\Api
 */
interface PayUGetCreditCardSecureFormConfigInterface
{
    /**
     * PayU SDK env key
     */
    const CONFIG_ENV = 'env';

    /**
     * Merchant POS ID key
     */
    const CONFIG_POS_ID = 'posId';

    /**
     * Store Card key
     */
    const CONFIG_STORE_CARD = 'storeCard';

    /**
     * Get Secure Form parameters
     *
     * @return array
     */
    public function execute();
}
