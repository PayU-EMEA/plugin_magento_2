<?php

namespace PayU\PaymentGateway\Gateway\Response;

use Magento\Payment\Gateway\Response\HandlerInterface;

/**
 * Class FraudHandler
 * @package PayU\PaymentGateway\Gateway\Response
 */
class FraudHandler extends AbstractTxnId implements HandlerInterface
{
    const FRAUD_MSG_LIST = 'FRAUD_MSG_LIST';

    /**
     * {@inheritdoc}
     */
    public function handle(array $handlingSubject, array $response)
    {
        if (!isset($response[self::FRAUD_MSG_LIST]) || !is_array($response[self::FRAUD_MSG_LIST])) {
            return;
        }
        parent::handle($handlingSubject, $response);
        $this->payment->setAdditionalInformation(self::FRAUD_MSG_LIST, (array)$response[self::FRAUD_MSG_LIST]);
        $this->payment->setIsTransactionPending(true);
        $this->payment->setIsFraudDetected(true);
    }
}
