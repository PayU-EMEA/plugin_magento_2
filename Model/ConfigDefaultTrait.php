<?php
namespace PayU\PaymentGateway\Model;

/**
 * Trait ConfigDefaultTrait
 * @package PayU\PaymentGateway\Model
 */
trait ConfigDefaultTrait
{
    /**
     * {@inheritdoc}
     */
    public function getCacheConfig()
    {
        return $this->cacheConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function getMultiCurrencyPricingPartnerId()
    {
        return $this->multiCurrencyPartnerId;
    }

    /**
     * {@inheritdoc}
     */
    public function getMerchantPosiId()
    {
        $config = $this->openPayUConfig;

        return $config::getMerchantPosId();
    }

    /**
     * {@inheritdoc}
     */
    public function setEnvironment($environment)
    {
        $config = $this->openPayUConfig;
        $config::setEnvironment($environment);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setMerchantPosId($merchantPosId)
    {
        $config = $this->openPayUConfig;
        $config::setMerchantPosId($merchantPosId);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setSignatureKey($signatureKey)
    {
        $config = $this->openPayUConfig;
        $config::setSignatureKey($signatureKey);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setOauthClientId($oAuthClientId)
    {
        $config = $this->openPayUConfig;
        $config::setOauthClientId($oAuthClientId);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setOauthClientSecret($oAuthClientSecret)
    {
        $config = $this->openPayUConfig;
        $config::setOauthClientSecret($oAuthClientSecret);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setOauthGrantType($oAuthGrantType)
    {
        $config = $this->openPayUConfig;
        $config::setOauthGrantType($oAuthGrantType);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setOauthEmail($email)
    {
        $config = $this->openPayUConfig;
        $config::setOauthEmail($email);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerExtId($customerId)
    {
        $config = $this->openPayUConfig;
        $config::setOauthExtCustomerId($customerId);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setSender($sender)
    {
        $config = $this->openPayUConfig;
        $config::setSender($sender);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setGatewayConfigCode($code)
    {
        $this->gatewayConfig->setMethodCode($code);

        return $this;
    }
}
