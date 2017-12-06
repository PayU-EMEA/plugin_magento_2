<?php

namespace PayU\PaymentGateway\Gateway\Response\Order;

use Magento\Payment\Gateway\Response\HandlerInterface;
use PayU\PaymentGateway\Api\PayUConfigInterface;
use PayU\PaymentGateway\Api\PayUCreateOrderInterface;
use PayU\PaymentGateway\Gateway\Response\AbstractTxnId;

/**
 * Class TxnIdCreateHandler
 * @package PayU\PaymentGateway\Gateway\Response
 */
class TxnIdCreateHandler extends AbstractTxnId implements HandlerInterface
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
                PayUConfigInterface::PAYU_REDIRECT_URI_CODE,
                $this->response[PayUCreateOrderInterface::REDIRECT_URI_FIELD]
            );
        }
    }
}
