<?php

namespace PayU\PaymentGateway\Model;

use PayU\PaymentGateway\Api\GetAvailableLocaleInterface;
use PayU\PaymentGateway\Api\PayUConfigInterface;
use PayU\PaymentGateway\Api\PayUGetCreditCardCVVWidgetConfigInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use PayU\PaymentGateway\Model\Ui\CardConfigProvider;

/**
 * Class GetCreditCardCVVWidgetConfig
 * @package PayU\PaymentGateway\Model
 */
class GetCreditCardCVVWidgetConfig implements PayUGetCreditCardCVVWidgetConfigInterface
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
     * GetCreditCardCVVWidgetConfig constructor.
     *
     * @param \OpenPayU_Configuration $openPayUConfig
     * @param GetAvailableLocaleInterface $availableLocale
     * @param PayUConfigInterface $payUConfig
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(
        \OpenPayU_Configuration $openPayUConfig,
        GetAvailableLocaleInterface $availableLocale,
        PayUConfigInterface $payUConfig,
        CheckoutSession $checkoutSession
    ) {
        $this->availableLocale = $availableLocale;
        $this->openPayUConfig = $openPayUConfig;
        $this->payUConfig = $payUConfig;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($cvvUrl)
    {
        $this->payUConfig->setDefaultConfig(CardConfigProvider::CODE);
        $config = $this->openPayUConfig;

        return [
            static::CONFIG_ENV => $config::getEnvironment(),
            static::CONFIG_POS_ID => $config::getMerchantPosId(),
            static::CONFIG_CVV_URL => $this->getCvvUrl($cvvUrl),
            static::CONFIG_LANGUAGE => $this->availableLocale->execute()

        ];
    }

    /**
     * Get refId part from ccv url for widget config
     *
     * @param string $cvvUrl
     *
     * @return string
     */
    private function getCvvUrl($cvvUrl)
    {
        $urlParams = explode('?', $cvvUrl);
        if (isset($urlParams[1])) {
            return $urlParams[1];
        }

        return '';
    }
}
