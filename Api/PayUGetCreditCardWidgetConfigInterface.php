<?php

namespace PayU\PaymentGateway\Api;

/**
 * Interface PayUGetCreditCardWidgetConfigInterface
 * @package PayU\PaymentGateway\Api
 */
interface PayUGetCreditCardWidgetConfigInterface
{
    /**
     * False key
     */
    const FALSE_KEY = 'false';

    /**
     * True key
     */
    const TRUE_KEY = 'true';

    /**
     * Script source key
     */
    const CONFIG_SRC_FIELD = 'src';

    /**
     * Merchant POS ID key
     */
    const CONFIG_MERCHANT_POS_ID_FIELD = 'merchant-pos-id';

    /**
     * Merchant shop name key
     */
    const CONFIG_SHOP_NAME_FIELD = 'shop-name';

    /**
     * Store Card key
     */
    const CONFIG_STORE_CARD_FIELD = 'store-card';

    /**
     * Recurring payment key
     */
    const CONFIG_RECURRING_PAYMENT_FIELD = 'recurring-payment';

    /**
     * PayU brand key
     */
    const CONFIG_PAYU_BRAND_FIELD = 'payu-brand';

    /**
     * Widget mode key
     */
    const CONFIG_WIDGET_MODE_FIELD = 'widget-mode';

    /**
     * Success callback key
     */
    const CONFIG_SUCCESS_CALLBACK_FIELD = 'success-callback';

    /**
     * Total amount key
     */
    const CONFIG_TOTAL_AMOUNT_FIELD = 'total-amount';

    /**
     * Currency code key
     */
    const CONFIG_CURRENCY_CODE_FIELD = 'currency-code';

    /**
     * Customer language key
     */
    const CONFIG_CUSTOMER_LANGUAGE_FIELD = 'customer-language';

    /**
     * Customer email key
     */
    const CONFIG_CUSTOMER_EMAIL_FIELD = 'customer-email';

    /**
     * Get widget parameters config for html <script>
     *
     * @param string|null $email
     * @param float|null $grandTotal
     * @param string|null $currencyCode
     *
     * @return array
     */
    public function execute($email = null, $grandTotal = null, $currencyCode = null);

    /**
     * Convert execute method to Json
     *
     * @return string
     */
    public function toJson();
}
