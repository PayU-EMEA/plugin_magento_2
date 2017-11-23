<?php

namespace PayU\PaymentGateway\Block\Account;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use PayU\PaymentGateway\Api\PayUGetUserPayMethodsInterface;
use Magento\Payment\Gateway\Config\Config as GatewayConfig;
use PayU\PaymentGateway\Model\Ui\CardConfigProvider;

/**
 * Customer account stored cards
 *
 * Class StoredCards
 * @package Payu\PaymentGateway\Block\Account
 */
class StoredCards extends Template
{
    /**
     * @var PayUGetUserPayMethodsInterface
     */
    private $userPayMethods;

    /**
     * @var GatewayConfig
     */
    private $gatewayConfig;

    /**
     * StoredCards constructor.
     *
     * @param Context $context
     * @param PayUGetUserPayMethodsInterface $userPayMethods
     * @param GatewayConfig $gatewayConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        PayUGetUserPayMethodsInterface $userPayMethods,
        GatewayConfig $gatewayConfig,
        array $data = []
    ) {
        $this->userPayMethods = $userPayMethods;
        $this->gatewayConfig = $gatewayConfig;
        parent::__construct($context, $data);
    }

    /**
     * Get user stored cards
     *
     * @return array
     */
    public function getStoredCards()
    {
        try {
            $this->gatewayConfig->setMethodCode(CardConfigProvider::CODE);
            $result = $this->userPayMethods->execute();
            if (array_key_exists(PayUGetUserPayMethodsInterface::CARD_TOKENS, $result) &&
                array_key_exists(PayUGetUserPayMethodsInterface::PEX_TOKENS, $result)) {
                if (!$result[PayUGetUserPayMethodsInterface::CARD_TOKENS]) {
                    $result[PayUGetUserPayMethodsInterface::CARD_TOKENS] = [];
                }
                if (!$result[PayUGetUserPayMethodsInterface::PEX_TOKENS]) {
                    $result[PayUGetUserPayMethodsInterface::PEX_TOKENS] = [];
                }

                return array_merge($result['cardTokens'], $result['pexTokens']);
            }

            return [];
        } catch (\OpenPayU_Exception $exception) {
            return [];
        }
    }
}
