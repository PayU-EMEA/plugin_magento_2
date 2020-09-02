<?php

namespace PayU\PaymentGateway\Api;

/**
 * Interface PayUGetCreditCardCVVWidgetConfigInterface
 * @package PayU\PaymentGateway\Api
 */
interface PayUGetCreditCardCVVWidgetConfigInterface
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
     * CVV url key
     */
    const CONFIG_CVV_URL = 'cvvUrl';

    /**
     * Language key
     */
    const CONFIG_LANGUAGE = 'language';

    /**
     * Get CVV Secure Form parameters
     *
     * @param string $cvvUrl
     *
     * @return array
     */
    public function execute($cvvUrl);
}
