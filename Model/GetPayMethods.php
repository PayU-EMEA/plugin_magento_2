<?php

namespace PayU\PaymentGateway\Model;

use PayU\PaymentGateway\Api\PayUGetPayMethodsInterface;
use PayU\PaymentGateway\Api\PayUConfigInterface;
use PayU\PaymentGateway\Api\GetAvailableLocaleInterface;
use PayU\PaymentGateway\Model\Logger\Logger;
use PayU\PaymentGateway\Model\Ui\CardConfigProvider;
use PayU\PaymentGateway\Model\Ui\ConfigProvider;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Payment\Gateway\Config\Config as GatewayConfig;

/**
 * Class GetPayMethods
 * @package PayU\PaymentGateway\Model
 */
class GetPayMethods implements PayUGetPayMethodsInterface
{
    /**
     * @var \OpenPayU_Retrieve
     */
    private $openPayURetrieve;

    /**
     * @var PayUConfigInterface
     */
    private $payUConfig;

    /**
     * @var GetAvailableLocaleInterface
     */
    private $availableLocale;

    /**
     * @var GatewayConfig
     */
    private $gatewayConfig;

    /**
     * @var int
     */
    private $storeId;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var array
     */
    private $result = [];

    /**
     * GetPayMethods constructor.
     *
     * @param \OpenPayU_Retrieve $openPayURetrieve
     * @param PayUConfigInterface $payUConfig
     * @param GetAvailableLocaleInterface $availableLocale
     * @param GatewayConfig $gatewayConfig
     * @param StoreManagerInterface $storeManager
     * @param Logger $logger
     */
    public function __construct(
        \OpenPayU_Retrieve $openPayURetrieve,
        PayUConfigInterface $payUConfig,
        GetAvailableLocaleInterface $availableLocale,
        GatewayConfig $gatewayConfig,
        StoreManagerInterface $storeManager,
        Logger $logger
    ) {
        $this->openPayURetrieve = $openPayURetrieve;
        $this->payUConfig = $payUConfig;
        $this->availableLocale = $availableLocale;
        $this->gatewayConfig = $gatewayConfig;
        $this->storeId = $storeManager->getStore()->getId();
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        try {
            $this->payUConfig->setDefaultConfig(ConfigProvider::CODE);
            $payURetrive = $this->openPayURetrieve;
            $response = $payURetrive::payMethods($this->availableLocale->execute())->getResponse();
            if (isset($response->payByLinks)) {
                $this->result =
                    $this->sortPaymentMethods($response->payByLinks, $this->payUConfig->getPaymentMethodsOrder());
                $this->gatewayConfig->setMethodCode(CardConfigProvider::CODE);
                if ((bool)$this->gatewayConfig->getValue('main_parameters/active', $this->storeId)) {
                    $this->removeCreditCard();
                }
                $this->removeTestPayment();
            }
        } catch (\OpenPayU_Exception $exception) {
            $this->logger->critical($exception->getMessage());
            $this->result = [];
        }

        return $this->result;
    }

    /**
     * Sort Payment Methods
     *
     * @param array $paymentMethods
     * @param array $paymentMethodsOrder
     *
     * @return array
     */
    private function sortPaymentMethods($paymentMethods, $paymentMethodsOrder)
    {
        if (count($paymentMethodsOrder) < 1) {
            return $paymentMethods;
        }
        array_walk(
            $paymentMethods,
            function ($item, $key, $paymentMethodsOrder) {
                if (array_key_exists($item->value, $paymentMethodsOrder)) {
                    $item->sort = $paymentMethodsOrder[$item->value];
                } else {
                    $item->sort = $key + 100;
                }
            },
            array_flip($paymentMethodsOrder)
        );
        usort(
            $paymentMethods,
            function ($a, $b) {
                return $a->sort - $b->sort;
            }
        );

        return $paymentMethods;
    }

    /**
     * Remove credit card from pay by links response
     *
     * @return void
     */
    private function removeCreditCard()
    {
        $this->result = array_filter(
            $this->result,
            function ($payByLink) {
                return $payByLink->value !== PayUGetPayMethodsInterface::CREDIT_CARD_CODE;
            }
        );
    }

    /**
     * Remove test payment when disabled
     *
     * @return void
     */
    private function removeTestPayment()
    {
        $this->result = array_filter(
            $this->result,
            function ($payByLink) {
                return !($payByLink->value === PayUGetPayMethodsInterface::TEST_PAYMENT_CODE && $payByLink->status !== PayUGetPayMethodsInterface::PAYMETHOD_STATUS_ENABLED);
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function toJson()
    {
        return json_encode($this->result);
    }
}
