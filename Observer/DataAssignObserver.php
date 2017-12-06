<?php

namespace PayU\PaymentGateway\Observer;

use Magento\Framework\Event\Observer;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Quote\Api\Data\PaymentInterface;
use PayU\PaymentGateway\Api\PayUConfigInterface;

/**
 * Class DataAssignObserver
 * @package PayU\PaymentGateway\Observer
 */
class DataAssignObserver extends AbstractDataAssignObserver
{
    /**
     * @var array
     */
    protected $additionalList = [
        PayUConfigInterface::PAYU_METHOD_CODE,
        PayUConfigInterface::PAYU_METHOD_TYPE_CODE
    ];

    /**
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        $data = $this->readDataArgument($observer);
        $additionalData = $data->getData(PaymentInterface::KEY_ADDITIONAL_DATA);
        if (!is_array($additionalData)) {
            return;
        }
        $paymentInfo = $this->readPaymentModelArgument($observer);
        foreach ($this->additionalList as $additionalKey) {
            if (isset($additionalData[$additionalKey])) {
                $paymentInfo->setAdditionalInformation($additionalKey, $additionalData[$additionalKey]);
            }
        }
    }
}
