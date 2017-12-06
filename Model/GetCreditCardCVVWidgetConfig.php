<?php

namespace PayU\PaymentGateway\Model;

use Magento\Sales\Api\OrderRepositoryInterface;
use PayU\PaymentGateway\Api\GetAvailableLocaleInterface;
use PayU\PaymentGateway\Api\PayUConfigInterface;
use PayU\PaymentGateway\Api\PayUGetCreditCardCVVWidgetConfigInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use PayU\PaymentGateway\Api\PayUGetCreditCardWidgetConfigInterface as PayuCreditCardConfig;
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
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * GetCreditCardWidgetConfig constructor.
     *
     * @param \OpenPayU_Configuration $openPayUConfig
     * @param GetAvailableLocaleInterface $availableLocale
     * @param PayUConfigInterface $payUConfig
     * @param CheckoutSession $checkoutSession
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        \OpenPayU_Configuration $openPayUConfig,
        GetAvailableLocaleInterface $availableLocale,
        PayUConfigInterface $payUConfig,
        CheckoutSession $checkoutSession,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->availableLocale = $availableLocale;
        $this->openPayUConfig = $openPayUConfig;
        $this->payUConfig = $payUConfig;
        $this->checkoutSession = $checkoutSession;
        $this->orderRepository = $orderRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($cvvUrl, $orderId = null)
    {
        $order = $this->checkoutSession->getLastRealOrder();
        if ($orderId !== null) {
            $order = $this->orderRepository->get($orderId);
        }
        $this->payUConfig->setDefaultConfig(CardConfigProvider::CODE);
        $serviceUrl = PayUConfigInterface::PAYU_SECURE_URL;
        $config = $this->openPayUConfig;
        if ($config::getEnvironment() === PayUConfigInterface::ENVIRONMENT_SANBOX) {
            $serviceUrl = PayUConfigInterface::PAYU_SANDBOX_URL;
        }
        $widgetConfig = [
            PayuCreditCardConfig::CONFIG_SRC_FIELD => $serviceUrl . '/front/widget/js/payu-bootstrap.js',
            PayuCreditCardConfig::CONFIG_MERCHANT_POS_ID_FIELD => $config::getMerchantPosId(),
            PayuCreditCardConfig::CONFIG_SHOP_NAME_FIELD => 'test',
            PayuCreditCardConfig::CONFIG_TOTAL_AMOUNT_FIELD => $order->getGrandTotal(),
            PayuCreditCardConfig::CONFIG_CURRENCY_CODE_FIELD => $order->getOrderCurrencyCode(),
            PayuCreditCardConfig::CONFIG_CUSTOMER_LANGUAGE_FIELD => $this->availableLocale->execute(
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
            static::CONFIG_WIDGET_TYPE_FIELD => 'cvv',
            static::CONFIG_WIDGET_CVV_URL_FIELD => $this->getCvvUrl($cvvUrl),
            static::CONFIG_WIDGET_CVV_SUCCESS_CALLBACK_FIELD => 'getTokenCallback',
        ];
        $widgetConfig['sig'] = $this->calculateSig($widgetConfig);

        return json_encode($widgetConfig);
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

    /**
     * Calculate sig for cvv widget config authorization
     *
     * @param array $widgetConfig
     *
     * @return string
     */
    private function calculateSig(array $widgetConfig)
    {
        $arrayToCalculate = [
            static::CONFIG_WIDGET_CVV_URL_FIELD,
            PayuCreditCardConfig::CONFIG_CURRENCY_CODE_FIELD,
            PayuCreditCardConfig::CONFIG_CUSTOMER_LANGUAGE_FIELD,
            PayuCreditCardConfig::CONFIG_MERCHANT_POS_ID_FIELD,
            PayuCreditCardConfig::CONFIG_SHOP_NAME_FIELD,
            PayuCreditCardConfig::CONFIG_TOTAL_AMOUNT_FIELD,
        ];
        $mergeParams = array_intersect_key($widgetConfig, array_flip($arrayToCalculate));
        ksort($mergeParams);
        $mergeParams = implode('', $mergeParams);
        $config = $this->openPayUConfig;

        return hash('sha256', $mergeParams . $config::getSignatureKey());
    }
}
