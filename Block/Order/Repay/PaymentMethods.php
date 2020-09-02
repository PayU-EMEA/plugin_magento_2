<?php

namespace PayU\PaymentGateway\Block\Order\Repay;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\Data\OrderInterface;
use PayU\PaymentGateway\Api\GetAvailableLocaleInterface;
use PayU\PaymentGateway\Api\PayUConfigInterface;
use PayU\PaymentGateway\Api\PayUGetCreditCardSecureFormConfigInterface;
use PayU\PaymentGateway\Api\PayUGetPayMethodsInterface;
use PayU\PaymentGateway\Api\PayUGetUserPayMethodsInterface;
use PayU\PaymentGateway\Model\Ui\CardConfigProvider;
use PayU\PaymentGateway\Model\Ui\ConfigProvider;
use Magento\Payment\Gateway\Config\Config as GatewayConfig;

/**
 * Class PaymentMethods
 * @package Payu\PaymentGateway\Block\Order\Repay
 */
class PaymentMethods extends Template
{
    /**
     * Code
     */
    const CODE = 'code';

    /**
     * Logo url
     */
    const LOGO_SRC = 'logoSrc';

    /**
     * Order ID
     */
    const ORDER_ID = 'orderId';

    /**
     * Locale
     */
    const LANGUAGE = 'language';

    /**
     * Terms URL
     */
    const TERMS_URL = 'termsUrl';

    /**
     * Transfer Key
     */
    const TRANSFER_KEY = 'transferKey';

    /**
     * Repay URL
     */
    const REPAY_URL = 'repayUrl';

    /**
     * Stored Cards List
     */
    const STORED_CARDS = 'storedCards';

    /**
     * Secure Form Config
     */
    const SECURE_FORM = 'secureForm';

    /**
     * Repay address URL
     */
    const REPAY_URI = 'sales/order/repay';

    /**
     * Active gateway code
     */
    const ACTIVE = 'main_parameters/active';

    /**
     * @var PayUGetPayMethodsInterface
     */
    private $payMethods;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var GetAvailableLocaleInterface
     */
    private $availableLocale;

    /**
     * @var PayUGetUserPayMethodsInterface
     */
    private $userPayMethods;

    /**
     * @var OrderInterface
     */
    private $order;

    /**
     * @var GatewayConfig
     */
    private $gatewayConfig;

    /**
     * @var PayUConfigInterface
     */
    private $payUConfig;

    /**
     * PaymentMethods constructor.
     *
     * @param Context $context
     * @param PayUGetPayMethodsInterface $payMethods
     * @param PayUGetCreditCardSecureFormConfigInterface $secureFormConfig
     * @param OrderRepositoryInterface $orderRepository
     * @param GetAvailableLocaleInterface $availableLocale
     * @param PayUGetUserPayMethodsInterface $userPayMethods
     * @param GatewayConfig $gatewayConfig
     * @param PayUConfigInterface $payUConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        PayUGetPayMethodsInterface $payMethods,
        PayUGetCreditCardSecureFormConfigInterface $secureFormConfig,
        OrderRepositoryInterface $orderRepository,
        GetAvailableLocaleInterface $availableLocale,
        PayUGetUserPayMethodsInterface $userPayMethods,
        GatewayConfig $gatewayConfig,
        PayUConfigInterface $payUConfig,
        array $data = []
    ) {
        $this->payMethods = $payMethods;
        $this->secureFormConfig = $secureFormConfig;
        $this->orderRepository = $orderRepository;
        $this->availableLocale = $availableLocale;
        $this->userPayMethods = $userPayMethods;
        $this->gatewayConfig = $gatewayConfig;
        $this->payUConfig = $payUConfig;
        parent::__construct($context, $data);
    }

    /**
     * Get config for PayU Payment Gateway
     *
     * @return string
     */
    public function getPaymentGatewayConfig()
    {
        $storeId = $this->_storeManager->getStore()->getId();
        $this->gatewayConfig->setMethodCode(ConfigProvider::CODE);
        if (!(bool)$this->gatewayConfig->getValue(static::ACTIVE, $storeId) &&
            !(bool)$this->payUConfig->isCrediCardCurrencyRates()) {
            return json_encode([]);
        }

        return json_encode(
            [
                static::CODE => ConfigProvider::CODE,
                static::LOGO_SRC => $this->getViewFileUrl(PayUConfigInterface::PAYU_BANK_TRANSFER_LOGO_SRC),
                static::ORDER_ID => $this->getOrder()->getEntityId(),
                static::LANGUAGE => $this->availableLocale->execute(),
                static::TERMS_URL => PayUConfigInterface::PAYU_TERMS_URL,
                static::TRANSFER_KEY => PayUConfigInterface::PAYU_BANK_TRANSFER_KEY,
                static::REPAY_URL => static::REPAY_URI,
                'methods' => $this->payMethods->execute()
            ]
        );
    }

    /**
     * Get config for PayU Card Payment Gateway
     *
     * @return string
     */
    public function getCardPaymentGatewayConfig()
    {
        $storeId = $this->_storeManager->getStore()->getId();
        $this->gatewayConfig->setMethodCode(CardConfigProvider::CODE);
        if (!(bool)$this->gatewayConfig->getValue(static::ACTIVE, $storeId)) {
            return json_encode([]);
        }

        $userPayMethods = $this->getUserStoredCards();

        return json_encode(
            [
                static::CODE => CardConfigProvider::CODE,
                static::LOGO_SRC => $this->getViewFileUrl(PayUConfigInterface::PAYU_CC_TRANSFER_LOGO_SRC),
                static::ORDER_ID => $this->getOrder()->getEntityId(),
                static::LANGUAGE => $this->availableLocale->execute(),
                static::TERMS_URL => PayUConfigInterface::PAYU_TERMS_URL,
                static::TRANSFER_KEY => PayUConfigInterface::PAYU_CC_TRANSFER_KEY,
                static::REPAY_URL => static::REPAY_URI,
                static::STORED_CARDS => array_key_exists(PayUGetUserPayMethodsInterface::CARD_TOKENS, $userPayMethods) && $userPayMethods[PayUGetUserPayMethodsInterface::CARD_TOKENS] ? $userPayMethods[PayUGetUserPayMethodsInterface::CARD_TOKENS] : [],
                static::SECURE_FORM => $this->secureFormConfig->execute()
            ]
        );
    }
    /**
     *
     * @return string
     */
    public function getCardEnv()
    {
        return $this->secureFormConfig->execute()[PayUGetCreditCardSecureFormConfigInterface::CONFIG_ENV];
    }

    /**
     * @return array
     */
    private function getUserStoredCards()
    {
        return $this->userPayMethods->execute(
            $this->getOrder()->getCustomerEmail(),
            $this->getOrder()->getCustomerId()
        );
    }

    /**
     * Get order
     *
     * @return OrderInterface
     */
    private function getOrder()
    {
        $orderId = (int)$this->getRequest()->getParam('order_id');
        if ($orderId !== null && $this->order === null) {
            $this->order = $this->orderRepository->get($orderId);
        }

        return $this->order;
    }
}
