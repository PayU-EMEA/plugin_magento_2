<?php

namespace PayU\PaymentGateway\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Store\Model\StoreManagerInterface;
use PayU\PaymentGateway\Api\PayUConfigInterface;
use PayU\PaymentGateway\Api\GetAvailableLocaleInterface;
use PayU\PaymentGateway\Api\PayUGetCreditCardWidgetConfigInterface;
use Magento\Framework\View\Asset\Repository as AssetRepository;
use Magento\Payment\Gateway\Config\Config as GatewayConfig;
use Magento\Framework\Json\EncoderInterface;
use PayU\PaymentGateway\Api\PayUGetUserPayMethodsInterface;

/**
 * Class ConfigProvider
 *
 * Provide configuration to checkout JS for pay with credit card method
 */
class CardConfigProvider implements ConfigProviderInterface
{
    /**
     * Pay with PayU Credit Card code
     */
    const CODE = 'payu_gateway_card';

    /**
     * @var PayUGetCreditCardWidgetConfigInterface
     */
    private $cardWidgetConfig;

    /**
     * @var AssetRepository
     */
    private $assetRepository;

    /**
     * @var GatewayConfig
     */
    private $gatewayConfig;

    /**
     * @var int
     */
    private $storeId;

    /**
     * @var GetAvailableLocaleInterface
     */
    private $availableLocale;

    /**
     * @var PayUGetUserPayMethodsInterface
     */
    private $userPayMethods;

    /**
     * @var EncoderInterface
     */
    private $encoder;

    /**
     * CardConfigProvider constructor.
     *
     * @param PayUGetCreditCardWidgetConfigInterface $cardConfig
     * @param AssetRepository $assetRepository
     * @param GatewayConfig $gatewayConfig
     * @param StoreManagerInterface $storeManager
     * @param GetAvailableLocaleInterface $availableLocale
     * @param PayUGetUserPayMethodsInterface $userPayMethods
     * @param EncoderInterface $encoder
     */
    public function __construct(
        PayUGetCreditCardWidgetConfigInterface $cardConfig,
        AssetRepository $assetRepository,
        GatewayConfig $gatewayConfig,
        StoreManagerInterface $storeManager,
        GetAvailableLocaleInterface $availableLocale,
        PayUGetUserPayMethodsInterface $userPayMethods,
        EncoderInterface $encoder
    ) {
        $this->cardWidgetConfig = $cardConfig;
        $this->assetRepository = $assetRepository;
        $this->gatewayConfig = $gatewayConfig;
        $this->storeId = $storeManager->getStore()->getId();
        $this->availableLocale = $availableLocale;
        $this->userPayMethods = $userPayMethods;
        $this->encoder = $encoder;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $this->gatewayConfig->setMethodCode(self::CODE);

        return [
            'payment' => [
                'payuGatewayCard' => [
                    'isActive' => (bool)$this->gatewayConfig->getValue('main_parameters/active', $this->storeId),
                    'logoSrc' => $this->assetRepository->getUrl(PayUConfigInterface::PAYU_CC_TRANSFER_LOGO_SRC),
                    'ccWidgetConfig' => $this->encoder->encode($this->cardWidgetConfig->execute()),
                    'storedCards' => $this->encoder->encode($this->userPayMethods->execute()),
                    'transferKey' => PayUConfigInterface::PAYU_CC_TRANSFER_KEY,
                    'termsUrl' => PayUConfigInterface::PAYU_TERMS_URL,
                    'locale' => $this->availableLocale->execute()
                ]
            ]
        ];
    }
}
