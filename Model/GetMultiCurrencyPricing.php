<?php

namespace PayU\PaymentGateway\Model;

use PayU\PaymentGateway\Api\PayUConfigInterface;
use PayU\PaymentGateway\Api\PayUGetMultiCurrencyPricingInterface;
use PayU\PaymentGateway\Model\Ui\CardConfigProvider;
use Magento\Framework\HTTP\ZendClientFactory;

/**
 * Class GetCreditCardCVVWidgetConfig
 * @package PayU\PaymentGateway\Model
 */
class GetMultiCurrencyPricing implements PayUGetMultiCurrencyPricingInterface
{

    /**
     * @var PayUConfigInterface
     */
    private $payUConfig;

    /**
     * @var \OpenPayU_Oauth
     */
    private $payUOauth;

    /**
     * @var \OpenPayU_Configuration
     */
    private $payUConfiguration;

    /**
     * @var ZendClientFactory
     */
    private $httpClientFactory;

    /**
     * GetMultiCurrencyPricing constructor.
     *
     * @param PayUConfigInterface $payUConfig
     * @param \OpenPayU_Oauth $payUOauth
     * @param \OpenPayU_Configuration $payUConfiguration
     * @param ZendClientFactory $httpClientFactory
     */
    public function __construct(
        PayUConfigInterface $payUConfig,
        \OpenPayU_Oauth $payUOauth,
        \OpenPayU_Configuration $payUConfiguration,
        ZendClientFactory $httpClientFactory
    ) {
        $this->payUConfig = $payUConfig;
        $this->payUOauth = $payUOauth;
        $this->payUConfiguration = $payUConfiguration;
        $this->httpClientFactory = $httpClientFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $this->payUConfig->setDefaultConfig(CardConfigProvider::CODE);
        $configuration = $this->payUConfiguration;
        $url =
            $configuration::getServiceUrl() .
            'mcp-partners/' .
            $this->payUConfig->getMultiCurrencyPricingPartnerId() .
            '/fx-table';
        $client = $this->httpClientFactory->create();
        $client->setUri($url);
        $client->setHeaders('Authorization', 'Bearer ' . $this->getClientCredentials());
        $client->setMethod(\Zend_Http_Client::GET);
        $response = $client->request()->getBody();

        return json_decode($response);
    }

    /**
     * Get OAuth Client Credentials
     *
     * @return string
     * @throws \OpenPayU_Exception_ServerError
     */
    private function getClientCredentials()
    {
        $this->payUConfig->setDefaultConfig(CardConfigProvider::CODE);
        $oauth = $this->payUOauth;
        $configuration = $this->payUConfiguration;
        /** @var \OauthResultClientCredentials $response */
        $response = $oauth::getAccessToken(
            $configuration::getOauthClientId(),
            $configuration::getOauthClientSecret()
        );

        return $response->getAccessToken();
    }
}
