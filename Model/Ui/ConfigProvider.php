<?php

namespace PayU\PaymentGateway\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Store\Model\StoreManagerInterface;
use PayU\PaymentGateway\Api\PayUConfigInterface;
use PayU\PaymentGateway\Api\GetAvailableLocaleInterface;
use PayU\PaymentGateway\Api\PayUGetPayMethodsInterface;
use Magento\Framework\View\Asset\Repository as AssetRepository;
use Magento\Payment\Gateway\Config\Config as GatewayConfig;
use Magento\Framework\Json\EncoderInterface;

/**
 * Class ConfigProvider
 *
 * Provide configuration to checkout JS for pay with pay by links method
 */
class ConfigProvider implements ConfigProviderInterface
{
    /**
     * Pay with PayU Pay by links code
     */
    const CODE = 'payu_gateway';

    /**
     * @var PayUGetPayMethodsInterface
     */
    private $payMethods;

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
     * @var EncoderInterface
     */
    private $encoder;

    /**
     * @var PayUConfigInterface
     */
    private $payUConfig;

    /**
     * ConfigProvider constructor.
     *
     * @param PayUGetPayMethodsInterface $payMethods
     * @param AssetRepository $assetRepository
     * @param GatewayConfig $gatewayConfig
     * @param StoreManagerInterface $storeManager
     * @param GetAvailableLocaleInterface $availableLocale
     * @param EncoderInterface $encoder
     * @param PayUConfigInterface $payUConfig
     */
    public function __construct(
        PayUGetPayMethodsInterface $payMethods,
        AssetRepository $assetRepository,
        GatewayConfig $gatewayConfig,
        StoreManagerInterface $storeManager,
        GetAvailableLocaleInterface $availableLocale,
        EncoderInterface $encoder,
        PayUConfigInterface $payUConfig
    ) {
        $this->payMethods = $payMethods;
        $this->assetRepository = $assetRepository;
        $this->gatewayConfig = $gatewayConfig;
        $this->storeId = $storeManager->getStore()->getId();
        $this->availableLocale = $availableLocale;
        $this->encoder = $encoder;
        $this->payUConfig = $payUConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $this->gatewayConfig->setMethodCode(self::CODE);
        $isActive =
            (bool)$this->gatewayConfig->getValue('main_parameters/active', $this->storeId) &&
            !(bool)$this->payUConfig->isCrediCardCurrencyRates();

        return [
            'payment' => [
                'payuGateway' => [
                    'isActive' => $isActive,
                    'logoSrc' => $this->assetRepository->getUrl(PayUConfigInterface::PAYU_BANK_TRANSFER_LOGO_SRC),
                    'termsUrl' => PayUConfigInterface::PAYU_TERMS_URL,
                    'payByLinks' => $this->encoder->encode($this->payMethods->execute()),
                    'transferKey' => PayUConfigInterface::PAYU_BANK_TRANSFER_KEY,
                    'locale' => $this->availableLocale->execute()
                ]
            ]
        ];
    }
}
