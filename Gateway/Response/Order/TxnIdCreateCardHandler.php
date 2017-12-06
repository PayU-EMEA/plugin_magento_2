<?php

namespace PayU\PaymentGateway\Gateway\Response\Order;

use Magento\Payment\Gateway\Response\HandlerInterface;
use PayU\PaymentGateway\Api\PayUConfigInterface;
use PayU\PaymentGateway\Api\PayUCreateOrderInterface;
use PayU\PaymentGateway\Gateway\Response\AbstractTxnId;

/**
 * Class TxnIdCreateCardHandler
 * @package PayU\PaymentGateway\Gateway\Response
 */
class TxnIdCreateCardHandler extends AbstractTxnId implements HandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(array $handlingSubject, array $response)
    {
        parent::handle($handlingSubject, $response);
        $this->payment->setTransactionId($this->response['orderId']);
        $this->payment->setIsTransactionClosed(false);
        if (array_key_exists(PayUCreateOrderInterface::REDIRECT_URI_FIELD, $this->response)) {
            $this->payment->setAdditionalInformation(
                $this->additionalInformationKeyMapper($this->response['status']->statusCode),
                $this->response[PayUCreateOrderInterface::REDIRECT_URI_FIELD]
            );
        }
    }

    /**
     * Get value for response code
     *
     * @param string $statusCode
     *
     * @return string
     */
    private function additionalInformationKeyMapper($statusCode)
    {
        $keyMap = [
            \OpenPayU_Order::SUCCESS => PayUConfigInterface::PAYU_REDIRECT_URI_CODE,
            PayUCreateOrderInterface::WARNING_CONTINUE_CVV => PayUConfigInterface::PAYU_SHOW_CVV_WIDGET,
            PayUCreateOrderInterface::WARNING_CONTINUE_3_DS => PayUConfigInterface::PAYU_REDIRECT_URI_CODE
        ];

        return array_key_exists($statusCode, $keyMap) ? $keyMap[$statusCode] :
            PayUConfigInterface::PAYU_REDIRECT_URI_CODE;
    }
}
