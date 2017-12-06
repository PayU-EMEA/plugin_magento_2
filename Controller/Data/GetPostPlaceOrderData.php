<?php

namespace PayU\PaymentGateway\Controller\Data;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Checkout\Model\Session;
use Magento\Customer\Model\Session as CustomerSession;
use PayU\PaymentGateway\Api\PayUConfigInterface;
use PayU\PaymentGateway\Api\PayUCreateOrderInterface;

/**
 * Class GetPostPlaceOrderData
 */
class GetPostPlaceOrderData extends Action
{
    /**
     * Success key
     */
    const SUCCESS_FIELD = 'success';

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * GetRedirectUrl constructor.
     *
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param Session $checkoutSession
     * @param CustomerSession $customerSession
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        Session $checkoutSession,
        CustomerSession $customerSession
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $returnData = [static::SUCCESS_FIELD => false];
        try {
            /** @var $payment \Magento\Sales\Model\Order\Payment */
            $payment = $this->checkoutSession->getLastRealOrder()->getPayment();
            $paymentInformation = $payment->getAdditionalInformation();
            if (is_array($paymentInformation) &&
                array_key_exists(PayUConfigInterface::PAYU_REDIRECT_URI_CODE, $paymentInformation)) {
                $returnData = [
                    static::SUCCESS_FIELD => true,
                    PayUCreateOrderInterface::REDIRECT_URI_FIELD => $paymentInformation[PayUConfigInterface::PAYU_REDIRECT_URI_CODE]
                ];
            } elseif (is_array($paymentInformation) &&
                array_key_exists(PayUConfigInterface::PAYU_SHOW_CVV_WIDGET, $paymentInformation)) {
                $this->customerSession->setCvvUrl(true);
                $returnData = [
                    static::SUCCESS_FIELD => true,
                    PayUCreateOrderInterface::REDIRECT_URI_FIELD => $this->_url->getUrl('checkout/onepage/continueCvv')
                ];
            } else {
                $returnData = [
                    static::SUCCESS_FIELD => true,
                    PayUCreateOrderInterface::REDIRECT_URI_FIELD => $this->_url->getUrl('checkout/onepage/success')
                ];
            }
        } catch (\Exception $exception) {
            $returnData['message'] = $exception->getMessage();
        }

        return $this->resultJsonFactory->create()->setData($returnData);
    }
}
