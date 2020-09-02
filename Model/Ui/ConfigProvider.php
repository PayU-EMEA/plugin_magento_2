<?php

namespace PayU\PaymentGateway\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Store\Model\StoreManagerInterface;
use PayU\PaymentGateway\Api\PayUConfigInterface;
use PayU\PaymentGateway\Api\PayUGetPayMethodsInterface;
use Magento\Framework\View\Asset\Repository as AssetRepository;
use Magento\Payment\Gateway\Config\Config as GatewayConfig;

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
     * @var ResolverInterface
     */
    private $resolver;

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
     * @param PayUConfigInterface $payUConfig
     * @param ResolverInterface $resolver
     */
    public function __construct(
        PayUGetPayMethodsInterface $payMethods,
        AssetRepository $assetRepository,
        GatewayConfig $gatewayConfig,
        StoreManagerInterface $storeManager,
        PayUConfigInterface $payUConfig,
        ResolverInterface $resolver
    ) {
        $this->payMethods = $payMethods;
        $this->assetRepository = $assetRepository;
        $this->gatewayConfig = $gatewayConfig;
        $this->storeId = $storeManager->getStore()->getId();
        $this->payUConfig = $payUConfig;
        $this->resolver = $resolver;
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
                    'payByLinks' => $this->payMethods->execute(),
                    'transferKey' => PayUConfigInterface::PAYU_BANK_TRANSFER_KEY,
                    'language' => $this->getLanguage()
                ]
            ]
        ];
    }

    private function getLanguage()
    {
        return current(explode('_', $this->resolver->getLocale()));
    }
}
