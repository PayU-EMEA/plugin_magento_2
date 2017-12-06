<?php

namespace PayU\PaymentGateway\Model;

use PayU\PaymentGateway\Api\GetAvailableLocaleInterface;
use PayU\PaymentGateway\Api\PayUConfigInterface;
use PayU\PaymentGateway\Api\PayUGetCreditCardWidgetConfigInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerSession;
use PayU\PaymentGateway\Model\Ui\CardConfigProvider;

/**
 * Class GetCreditCardWidgetConfig
 * @package PayU\PaymentGateway\Model
 */
class GetCreditCardWidgetConfig implements PayUGetCreditCardWidgetConfigInterface
{
    /**
     * @var GetAvailableLocaleInterface
     */
    private $availableLocale;

    /**
     * @var \OpenPayU_Configuration
     */
    private $openPayUConfig;

    /**
     * @var PayUConfigInterface
     */
    private $payUConfig;

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var array
     */
    private $result = [];

    /**
     * GetCreditCardWidgetConfig constructor.
     *
     * @param \OpenPayU_Configuration $openPayUConfig
     * @param GetAvailableLocaleInterface $availableLocale
     * @param PayUConfigInterface $payUConfig
     * @param CheckoutSession $checkoutSession
     * @param CustomerSession $customerSession
     */
    public function __construct(
        \OpenPayU_Configuration $openPayUConfig,
        GetAvailableLocaleInterface $availableLocale,
        PayUConfigInterface $payUConfig,
        CheckoutSession $checkoutSession,
        CustomerSession $customerSession
    ) {
        $this->availableLocale = $availableLocale;
        $this->openPayUConfig = $openPayUConfig;
        $this->payUConfig = $payUConfig;
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($email = null, $grandTotal = null, $currencyCode = null)
    {
        $quote = $this->checkoutSession->getQuote();
        $this->payUConfig->setDefaultConfig(CardConfigProvider::CODE);
        $serviceUrl = PayUConfigInterface::PAYU_SECURE_URL;
        $config = $this->openPayUConfig;
        if ($config::getEnvironment() === PayUConfigInterface::ENVIRONMENT_SANBOX) {
            $serviceUrl = PayUConfigInterface::PAYU_SANDBOX_URL;
        }
        $customerEmail = $quote->getCustomerEmail();
        $storeCard = static::TRUE_KEY;
        if (!$this->customerSession->isLoggedIn()) {
            $customerEmail = $email;
            $storeCard = static::FALSE_KEY;
        }
        if (!$this->payUConfig->isStoreCardEnable()) {
            $storeCard = static::FALSE_KEY;
        }
        $this->result = [
            static::CONFIG_SRC_FIELD => $serviceUrl . '/front/widget/js/payu-bootstrap.js',
            static::CONFIG_MERCHANT_POS_ID_FIELD => $config::getMerchantPosId(),
            static::CONFIG_SHOP_NAME_FIELD => 'test',
            static::CONFIG_STORE_CARD_FIELD => $storeCard,
            static::CONFIG_RECURRING_PAYMENT_FIELD => static::FALSE_KEY,
            static::CONFIG_PAYU_BRAND_FIELD => static::FALSE_KEY,
            static::CONFIG_WIDGET_MODE_FIELD => 'use',
            static::CONFIG_SUCCESS_CALLBACK_FIELD => 'getTokenCallback',
            static::CONFIG_TOTAL_AMOUNT_FIELD => $grandTotal === null ? $quote->getGrandTotal() : $grandTotal,
            static::CONFIG_CURRENCY_CODE_FIELD => $currencyCode === null ? $quote->getQuoteCurrencyCode() :
                $currencyCode,
            static::CONFIG_CUSTOMER_LANGUAGE_FIELD => $this->availableLocale->execute(
                [
                    'pl',
                    'en',
                    'cs',
                    'de',
                    'es',
                    'fr',
                    'it',
                    'pt'
                ]
            ),
            static::CONFIG_CUSTOMER_EMAIL_FIELD => $customerEmail,
        ];
        $this->result['sig'] = $this->calculateSig();

        return $this->result;
    }

    /**
     * {@inheritdoc}
     */
    public function toJson()
    {
        return json_encode($this->result);
    }

    /**
     * Calculate sig for widget config authorization
     *
     * @return string
     */
    private function calculateSig()
    {
        $arrayToCalculate = [
            static::CONFIG_CURRENCY_CODE_FIELD,
            static::CONFIG_CUSTOMER_EMAIL_FIELD,
            static::CONFIG_CUSTOMER_LANGUAGE_FIELD,
            static::CONFIG_MERCHANT_POS_ID_FIELD,
            static::CONFIG_PAYU_BRAND_FIELD,
            static::CONFIG_RECURRING_PAYMENT_FIELD,
            static::CONFIG_SHOP_NAME_FIELD,
            static::CONFIG_STORE_CARD_FIELD,
            static::CONFIG_TOTAL_AMOUNT_FIELD,
            static::CONFIG_WIDGET_MODE_FIELD
        ];
        $mergeParams = array_intersect_key($this->result, array_flip($arrayToCalculate));
        ksort($mergeParams);
        $mergeParams = implode('', $mergeParams);
        $config = $this->openPayUConfig;

        return hash('sha256', $mergeParams . $config::getSignatureKey());
    }
}
