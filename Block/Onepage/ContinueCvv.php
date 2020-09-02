<?php

namespace PayU\PaymentGateway\Block\Onepage;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use PayU\PaymentGateway\Api\PayUGetCreditCardCVVWidgetConfigInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerSession;
use PayU\PaymentGateway\Api\PayUConfigInterface;

/**
 * One page checkout warning continue ccv page
 *
 * Class ContinueCvv
 * @package Payu\PaymentGateway\Block\Onepage
 */
class ContinueCvv extends Template
{
    /**
     * @var PayUGetCreditCardCVVWidgetConfigInterface
     */
    private $cardWidgetCVVConfig;

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var CustomerSession\
     */
    private $customerSession;

    /**
     * ContinueCvv constructor.
     *
     * @param Context $context
     * @param PayUGetCreditCardCVVWidgetConfigInterface $cardWidgetCVVConfig
     * @param CheckoutSession $checkoutSession
     * @param CustomerSession $customerSession
     * @param array $data
     */
    public function __construct(
        Context $context,
        PayUGetCreditCardCVVWidgetConfigInterface $cardWidgetCVVConfig,
        CheckoutSession $checkoutSession,
        CustomerSession $customerSession,
        array $data = []
    ) {
        $this->cardWidgetCVVConfig = $cardWidgetCVVConfig;
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        parent::__construct($context, $data);
    }

    /**
     * Get config string for CVV widget
     *
     * @return string
     */
    public function getWidgetCvvConfig()
    {
        $cvv = $this->customerSession->getCvvUrl();
        $this->customerSession->setCvvUrl(null);
        $payment = $this->checkoutSession->getLastRealOrder()->getPayment();
        $paymentInformation = $payment->getAdditionalInformation();
        if (is_array($paymentInformation) &&
            array_key_exists(PayUConfigInterface::PAYU_SHOW_CVV_WIDGET, $paymentInformation) &&
            $cvv !== null) {

            $secureFormCvvConfig = $this->cardWidgetCVVConfig->execute($paymentInformation[PayUConfigInterface::PAYU_SHOW_CVV_WIDGET]);
            $secureFormCvvConfig['redirectUri'] = $this->getUrl('checkout/onepage/success');

            return json_encode($secureFormCvvConfig);
        }

        return json_encode([]);
    }

    /**
     *
     * @return string
     */
    public function getEnv()
    {
        return $this->cardWidgetCVVConfig->execute('')[PayUGetCreditCardCVVWidgetConfigInterface::CONFIG_ENV];
    }

}
