<?php

namespace PayU\PaymentGateway\Model;

use Magento\Framework\Exception\LocalizedException;
use PayU\PaymentGateway\Api\PayUConfigInterface;
use PayU\PaymentGateway\Api\PayUDeleteUserTokenInterface;
use PayU\PaymentGateway\Model\Ui\CardConfigProvider;
use Magento\Customer\Model\Session as CustomerSession;

/**
 * Class GetPayMethods
 * @package PayU\PaymentGateway\Model
 */
class DeleteUserToken implements PayUDeleteUserTokenInterface
{
    /**
     * @var \OpenPayU_Token
     */
    private $payUToken;

    /**
     * @var PayUConfigInterface
     */
    private $payUConfig;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * DeleteUserToken constructor.
     *
     * @param \OpenPayU_Token $payUToken
     * @param PayUConfigInterface $payUConfig
     * @param CustomerSession $customerSession
     */
    public function __construct(
        \OpenPayU_Token $payUToken,
        PayUConfigInterface $payUConfig,
        CustomerSession $customerSession
    ) {
        $this->payUToken = $payUToken;
        $this->payUConfig = $payUConfig;
        $this->customerSession = $customerSession;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($userToken)
    {
        try {
            $this->payUConfig->setDefaultConfig(CardConfigProvider::CODE);
            $this->payUConfig->setOauthGrantType(PayUConfigInterface::GRANT_TYPE_TRUSTED_MERCHANT);
            $this->payUConfig->setOauthEmail($this->customerSession->getCustomer()->getEmail());
            $this->payUConfig->setCustomerExtId($this->customerSession->getCustomerId());
            $token = $this->payUToken;

            return $token::delete($userToken);
        } catch (\OpenPayU_Exception $exception) {
            throw new LocalizedException(__($exception->getMessage()));
        }
    }
}
