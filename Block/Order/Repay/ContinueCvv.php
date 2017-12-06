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
        $orderId = (int)$this->getRequest()->getParam('order_id');
        if ($cvv !== null) {
            $widgetConfig = $this->cardWidgetCVVConfig->execute($cvv, $orderId);
            $jsonDecodeWidget = json_decode($widgetConfig, true);
            array_walk(
                $jsonDecodeWidget,
                function (&$item, $value) {
                    $item = sprintf('%s="%s"', $value, $item);
                }
            );

            return implode(' ', $jsonDecodeWidget);
        }

        return null;
    }

    /**
     * Return redirect url after success cvv action
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->getUrl('sales/order/history');
    }
}
