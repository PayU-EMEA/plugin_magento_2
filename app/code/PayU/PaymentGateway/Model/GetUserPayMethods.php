<?php

namespace PayU\PaymentGateway\Model;

use PayU\PaymentGateway\Api\PayUGetUserPayMethodsInterface;
use PayU\PaymentGateway\Api\PayUConfigInterface;
use PayU\PaymentGateway\Api\GetAvailableLocaleInterface;
use PayU\PaymentGateway\Model\Logger\Logger;
use PayU\PaymentGateway\Model\Ui\CardConfigProvider;
use Magento\Checkout\Model\Session;
use Magento\Customer\Model\Session as CustomerSession;

/**
 * Class GetPayMethods
 * @package PayU\PaymentGateway\Model
 */
class GetUserPayMethods implements PayUGetUserPayMethodsInterface
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
     * @var Session
     */
    private $checkoutSession;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var array
     */
    private $result = [];

    /**
     * @var Logger
     */
    private $logger;

    /**
     * GetPayMethods constructor.
     *
     * @param \OpenPayU_Retrieve $openPayURetrieve
     * @param PayUConfigInterface $payUConfig
     * @param GetAvailableLocaleInterface $availableLocale
     * @param Session $checkoutSession
     * @param CustomerSession $customerSession
     * @param Logger $logger
     */
    public function __construct(
        \OpenPayU_Retrieve $openPayURetrieve,
        PayUConfigInterface $payUConfig,
        GetAvailableLocaleInterface $availableLocale,
        Session $checkoutSession,
        CustomerSession $customerSession,
        Logger $logger
    ) {
        $this->openPayURetrieve = $openPayURetrieve;
        $this->payUConfig = $payUConfig;
        $this->availableLocale = $availableLocale;
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($email = null, $customerId = null)
    {
        if ($email !== null) {
            $customerEmail = $email;
        } else {
            $customerEmail = $this->checkoutSession->getQuote()->getCustomerEmail();
        }
        if (!$this->payUConfig->isStoreCardEnable() ||
            $customerEmail === null ||
            !$this->customerSession->isLoggedIn()) {
            return [];
        }
        try {
            $this->payUConfig->setDefaultConfig(CardConfigProvider::CODE);
            $this->payUConfig->setOauthGrantType(PayUConfigInterface::GRANT_TYPE_TRUSTED_MERCHANT);
            $this->payUConfig->setOauthEmail($customerEmail);
            $this->payUConfig->setCustomerExtId(
                $customerId === null ? $this->customerSession->getCustomerId() : $customerId
            );
            $payURetrieve = $this->openPayURetrieve;
            $response = $payURetrieve::payMethods($this->availableLocale->execute())->getResponse();
            if (isset($response->cardTokens) && isset($response->pexTokens)) {
                $this->result = [
                    static::CARD_TOKENS => $response->cardTokens,
                    static::PEX_TOKENS => $response->pexTokens
                ];
            }
        } catch (\OpenPayU_Exception $exception) {
            $this->logger->critical($exception->getMessage());
            $this->result = [];
        }

        return $this->result;
    }

    /**
     * {@inheritdoc}
     */
    public function toJson()
    {
        return json_encode($this->result);
    }

}
