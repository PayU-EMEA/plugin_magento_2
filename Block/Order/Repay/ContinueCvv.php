<?php

namespace PayU\PaymentGateway\Block\Order\Repay;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\Session as CustomerSession;
use PayU\PaymentGateway\Api\PayUGetCreditCardCVVWidgetConfigInterface;

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
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * ContinueCvv constructor.
     *
     * @param Context $context
     * @param PayUGetCreditCardCVVWidgetConfigInterface $cardWidgetCVVConfig
     * @param CustomerSession $customerSession
     * @param array $data
     */
    public function __construct(
        Context $context,
        PayUGetCreditCardCVVWidgetConfigInterface $cardWidgetCVVConfig,
        CustomerSession $customerSession,
        array $data = []
    ) {
        $this->cardWidgetCVVConfig = $cardWidgetCVVConfig;
        $this->customerSession = $customerSession;
        parent::__construct($context, $data);
    }

    /**
     * Get config string for CVV widget
     *
     * @return string|null
     */
    public function getWidgetCvvConfig()
    {
        $cvv = $this->customerSession->getCvvUrl();
        $this->customerSession->setCvvUrl(null);
        if ($cvv !== null) {
            $secureFormCvvConfig = $this->cardWidgetCVVConfig->execute($cvv);
            $secureFormCvvConfig['redirectUri'] = $this->getUrl('sales/order/history');
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
