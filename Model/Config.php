<?php

namespace PayU\PaymentGateway\Model;

use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use PayU\PaymentGateway\Api\PayUConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Payment\Gateway\Config\Config as GatewayConfig;
use PayU\PaymentGateway\Api\PayUCacheConfigInterface;
use PayU\PaymentGateway\Model\Ui\CardConfigProvider;

/**
 * Class Config
 * @package PayU\PaymentGateway\Model
 */
class Config implements PayUConfigInterface
{
    use ConfigDefaultTrait;
    /**
     * Config POS Parameters group
     */
    const GROUP_POS_PARAMETERS = 'pos_parameters';

    /**
     * Current Plugin Version
     */
    const PLUGIN_VERSION = '1.2.0';

    /**
     * @var \OpenPayU_Configuration
     */
    private $openPayUConfig;

    /**
     * @var GatewayConfig
     */
    private $gatewayConfig;

    /**
     * @var PayUCacheConfigInterface
     */
    private $cacheConfig;

    /**
     * @var int
     */
    private $storeId;

    /**
     * @var EncryptorInterface
     */
    private $encryptor;

    /**
     * @var ProductMetadataInterface
     */
    private $metadata;

    /**
     * @var string
     */
    private $multiCurrencyPartnerId = '';

    /**
     * @var array
     */
    private $environmentTypes = [PayUConfigInterface::ENVIRONMENT_SECURE, PayUConfigInterface::ENVIRONMENT_SANBOX];

    /**
     * Config constructor.
     *
     * @param \OpenPayU_Configuration $openPayUConfig
     * @param GatewayConfig $gatewayConfig
     * @param PayUCacheConfigInterface $cacheConfig
     * @param StoreManagerInterface $storeManager
     * @param EncryptorInterface $encryptor
     * @param ProductMetadataInterface $metadata
     */
    public function __construct(
        \OpenPayU_Configuration $openPayUConfig,
        GatewayConfig $gatewayConfig,
        PayUCacheConfigInterface $cacheConfig,
        StoreManagerInterface $storeManager,
        EncryptorInterface $encryptor,
        ProductMetadataInterface $metadata
    ) {
        $this->openPayUConfig = $openPayUConfig;
        $this->gatewayConfig = $gatewayConfig;
        $this->cacheConfig = $cacheConfig;
        $this->storeId = $storeManager->getStore()->getId();
        $this->encryptor = $encryptor;
        $this->metadata = $metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultConfig($code, $storeId = null)
    {
        if (!$this->isActive($code)) {
            $this->setMerchantPosId('');

            return $this;
        }
        if ($storeId !== null) {
            $this->storeId = $storeId;
        }
        try {
            $environment = $this->getGatewayConfig('main_parameters', 'environment');
            if ($environment !== null && array_key_exists($environment, $this->environmentTypes)) {
                $environment = $this->environmentTypes[$environment];
            }
            $this->setEnvironment($environment);
            $environmentSuffix = '';
            if ($environment === PayUConfigInterface::ENVIRONMENT_SANBOX) {
                $environmentSuffix = $environment . '_';
            }
            $this->setMerchantPosId(
                $this->getGatewayConfig(static::GROUP_POS_PARAMETERS, 'pos_id', $environmentSuffix)
            );
            $this->setSignatureKey(
                $this->getGatewayConfig(static::GROUP_POS_PARAMETERS, 'second_key', $environmentSuffix)
            );
            $this->setOauthClientId(
                $this->getGatewayConfig(static::GROUP_POS_PARAMETERS, 'client_id', $environmentSuffix)
            );
            $this->setOauthClientSecret(
                $this->getGatewayConfig(static::GROUP_POS_PARAMETERS, 'client_secret', $environmentSuffix)
            );
            $this->setOauthGrantType(PayUConfigInterface::GRANT_TYPE_CLIENT_CREDENTIALS);
            if ($code === CardConfigProvider::CODE) {
                $this->multiCurrencyPartnerId =
                    $this->getGatewayConfig(static::GROUP_POS_PARAMETERS, 'mcp_partner_id', $environmentSuffix);
            }
            $this->setSender('Magento 2 ver ' . $this->metadata->getVersion() . '/Plugin ver ' . static::PLUGIN_VERSION);
        } catch (\OpenPayU_Exception_Configuration $exception) {
            return $this;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isStoreCardEnable()
    {
        return (bool)$this->gatewayConfig->getValue('store_card', $this->storeId);
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentMethodsOrder()
    {
        return explode(
            ',',
            str_replace(
                ' ',
                '',
                $this->gatewayConfig->getValue('main_parameters/payment_methods_order', $this->storeId)
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isRepaymentActive($code)
    {
        $this->setGatewayConfigCode($code);

        return (bool)$this->gatewayConfig->getValue('repayment', $this->storeId);
    }

    /**
     * {@inheritdoc}
     */
    public function isCrediCardCurrencyRates()
    {
        $this->setGatewayConfigCode(CardConfigProvider::CODE);

        return (bool)$this->gatewayConfig->getValue('currency_rates', $this->storeId);
    }

    /**
     * {@inheritdoc}
     */
    public function getPluginVersion()
    {
        return static::PLUGIN_VERSION;
    }

    /**
     * Check if selected pay method is enable
     *
     * @param string $code
     *
     * @return bool
     */
    private function isActive($code)
    {
        $this->setGatewayConfigCode($code);

        return (bool)$this->gatewayConfig->getValue('main_parameters/active', $this->storeId);
    }

    /**
     * Get gateway config from magento configuration
     *
     * @param string $group
     * @param string $field
     * @param string $environmentSuffix
     *
     * @return string|null
     */
    private function getGatewayConfig($group, $field, $environmentSuffix = '')
    {
        $value = $this->gatewayConfig->getValue(
            sprintf('%s%s/%1$s%s', $environmentSuffix, $group, $field),
            $this->storeId
        );

        return $value;
    }
}
