<?php

namespace PayU\PaymentGateway\Api;

/**
 * Interface PayUConfigInterface
 * @package PayU\PaymentGateway\Api
 */
interface PayUConfigInterface
{
    /**
     * PayU Secure url
     */
    const PAYU_SECURE_URL = 'https://secure.payu.com';

    /**
     * PayU Sanbox url
     */
    const PAYU_SANDBOX_URL = 'https://secure.snd.payu.com';

    /**
     * Method code key
     */
    const PAYU_METHOD_CODE = 'payu_method';

    /**
     * Method type key
     */
    const PAYU_METHOD_TYPE_CODE = 'payu_method_type';

    /**
     * Redirect url key
     */
    const PAYU_REDIRECT_URI_CODE = 'payu_redirect_uri';

    /**
     * Show CCV Widget key
     */
    const PAYU_SHOW_CVV_WIDGET = 'payuShowCvvWidget';

    /**
     * Sanbox code
     */
    const ENVIRONMENT_SANBOX = 'sandbox';

    /**
     * Secure code
     */
    const ENVIRONMENT_SECURE = 'secure';

    /**
     * Grant type code
     */
    const GRANT_TYPE_GRANT_TYPE = 'grant_type';

    /**
     * Client Credentials code
     */
    const GRANT_TYPE_CLIENT_CREDENTIALS = 'client_credentials';

    /**
     * Trusted Merchant code
     */
    const GRANT_TYPE_TRUSTED_MERCHANT = 'trusted_merchant';

    /**
     * Terms url
     */
    const PAYU_TERMS_URL = 'http://static.payu.com/sites/terms/files/payu_terms_of_service_single_transaction_pl_pl.pdf';

    /**
     * PayU logo static link
     */
    const PAYU_BANK_TRANSFER_LOGO_SRC = 'PayU_PaymentGateway::images/payu_logo.png';

    /**
     * PayU card logo static link
     */
    const PAYU_CC_TRANSFER_LOGO_SRC = 'PayU_PaymentGateway::images/payu_cards.png';

    /**
     * Pay by links code
     */
    const PAYU_BANK_TRANSFER_KEY = 'PBL';

    /**
     * Pay by card code
     */
    const PAYU_CC_TRANSFER_KEY = 'CARD_TOKEN';

    /**
     * Get cache config
     *
     * @return PayUCacheConfigInterface
     */
    public function getCacheConfig();

    /**
     * Get Merchant POS ID
     *
     * @return string
     */
    public function getMerchantPosiId();

    /**
     * Set Environment SECURE|SANDBOX
     *
     * @param string|null $environment
     *
     * @return $this
     */
    public function setEnvironment($environment);

    /**
     * Set merchant POS ID
     *
     * @param string $merchantPosId
     *
     * @return $this
     */
    public function setMerchantPosId($merchantPosId);

    /**
     * Set signature key
     *
     * @param string $signatureKey
     *
     * @return $this
     */
    public function setSignatureKey($signatureKey);

    /**
     * Set OAuth client ID
     *
     * @param string $oAuthClientId
     *
     * @return $this
     */
    public function setOauthClientId($oAuthClientId);

    /**
     * Set OAuth client secret
     *
     * @param string $oAuthClientSecret
     *
     * @return $this
     */
    public function setOauthClientSecret($oAuthClientSecret);

    /**
     * Set OAuth grant type
     *
     * @param string $oAuthGrantType
     *
     * @return $this
     */
    public function setOauthGrantType($oAuthGrantType);

    /**
     * Set OAuth email
     *
     * @param string $email
     *
     * @return $this
     */
    public function setOauthEmail($email);

    /**
     * Set customer external ID
     *
     * @param int $customerId
     *
     * @return $this
     */
    public function setCustomerExtId($customerId);

    /**
     * Set default config
     *
     * @param string $code
     * @param int|null $storeId
     *
     * @return $this
     */
    public function setDefaultConfig($code, $storeId = null);

    /**
     * Set gateway config code
     *
     * @param string $code
     *
     * @return $this
     */
    public function setGatewayConfigCode($code);

    /**
     * Set sender
     *
     * @param string $sender
     *
     * @return $this
     */
    public function setSender($sender);

    /**
     * Check if credit card store per user is enable
     *
     * @return bool
     */
    public function isStoreCardEnable();

    /**
     * Check if repayment is enable
     *
     * @param string $code
     *
     * @return bool
     */
    public function isRepaymentActive($code);

    /**
     * Get Multi Currency Pricing Partner ID
     *
     * @return string
     */
    public function getMultiCurrencyPricingPartnerId();

    /**
     * Get payment methods order
     *
     * @return array
     */
    public function getPaymentMethodsOrder();

    /**
     * Get is enable currency rates for credit card
     *
     * @return bool
     */
    public function isCrediCardCurrencyRates();

    /**
     * Get plugin version
     *
     * @return string
     */
    public function getPluginVersion();

}
