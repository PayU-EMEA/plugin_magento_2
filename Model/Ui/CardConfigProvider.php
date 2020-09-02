<?php

namespace PayU\PaymentGateway\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\StoreManagerInterface;
use PayU\PaymentGateway\Api\PayUConfigInterface;
use PayU\PaymentGateway\Api\PayUGetCreditCardSecureFormConfigInterface;
use Magento\Framework\View\Asset\Repository as AssetRepository;
use Magento\Payment\Gateway\Config\Config as GatewayConfig;
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
     * @var PayUGetCreditCardSecureFormConfigInterface
     */
    private $secureFormConfig;

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
     * @var PayUGetUserPayMethodsInterface
     */
    private $userPayMethods;

    /**
     * @var ResolverInterface
     */
    private $resolver;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * CardConfigProvider constructor.
     *
     * @param PayUGetCreditCardSecureFormConfigInterface $secureFormConfig
     * @param AssetRepository $assetRepository
     * @param GatewayConfig $gatewayConfig
     * @param StoreManagerInterface $storeManager
     * @param PayUGetUserPayMethodsInterface $userPayMethods
     * @param SerializerInterface $serializer
     * @param ResolverInterface $resolver
     */
    public function __construct(
        PayUGetCreditCardSecureFormConfigInterface $secureFormConfig,
        AssetRepository $assetRepository,
        GatewayConfig $gatewayConfig,
        StoreManagerInterface $storeManager,
        PayUGetUserPayMethodsInterface $userPayMethods,
        SerializerInterface $serializer,
        ResolverInterface $resolver
    ) {
        $this->secureFormConfig = $secureFormConfig;
        $this->assetRepository = $assetRepository;
        $this->gatewayConfig = $gatewayConfig;
        $this->storeId = $storeManager->getStore()->getId();
        $this->userPayMethods = $userPayMethods;
        $this->serializer = $serializer;
        $this->resolver = $resolver;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $this->gatewayConfig->setMethodCode(self::CODE);

        $userPayMethods = $this->userPayMethods->execute();

        return [
            'payment' => [
                'payuGatewayCard' => [
                    'isActive' => (bool)$this->gatewayConfig->getValue('main_parameters/active', $this->storeId),
                    'logoSrc' => $this->assetRepository->getUrl(PayUConfigInterface::PAYU_CC_TRANSFER_LOGO_SRC),
                    'secureFormConfig' => $this->secureFormConfig->execute(),
                    'storedCards' => array_key_exists(PayUGetUserPayMethodsInterface::CARD_TOKENS, $userPayMethods) && $userPayMethods[PayUGetUserPayMethodsInterface::CARD_TOKENS] ? $userPayMethods[PayUGetUserPayMethodsInterface::CARD_TOKENS] : [],
                    'transferKey' => PayUConfigInterface::PAYU_CC_TRANSFER_KEY,
                    'termsUrl' => PayUConfigInterface::PAYU_TERMS_URL,
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
