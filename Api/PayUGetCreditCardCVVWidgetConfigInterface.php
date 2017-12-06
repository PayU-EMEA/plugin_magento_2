<?php

namespace PayU\PaymentGateway\Api;

/**
 * Interface PayUGetCreditCardCVVWidgetConfigInterface
 * @package PayU\PaymentGateway\Api
 */
interface PayUGetCreditCardCVVWidgetConfigInterface
{
    /**
     * Widget type key
     */
    const CONFIG_WIDGET_TYPE_FIELD = 'widget-type';

    /**
     * CVV url key
     */
    const CONFIG_WIDGET_CVV_URL_FIELD = 'cvv-url';

    /**
     * CVV success callbeck key
     */
    const CONFIG_WIDGET_CVV_SUCCESS_CALLBACK_FIELD = 'cvv-success-callback';

    /**
     * Get CVV widget parameters config for html <script>
     *
     * @param string $cvvUrl
     * @param string $orderId
     *
     * @return string
     */
    public function execute($cvvUrl, $orderId = null);
}
