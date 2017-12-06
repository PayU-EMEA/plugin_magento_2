<?php

namespace PayU\PaymentGateway\Controller\Data;

use PayU\PaymentGateway\Api\AcceptOrderPaymentInterface;
use PayU\PaymentGateway\Api\CancelOrderPaymentInterface;
use PayU\PaymentGateway\Api\WaitingOrderPaymentInterface;
use Magento\Payment\Gateway\Command\CommandException;

/**
 * Class NotifyOrderProcessor
 * @package PayU\PaymentGateway\Controller\Data
 */
class NotifyOrderProcessor
{
    /**
     * @var AcceptOrderPaymentInterface
     */
    private $acceptOrderPayment;

    /**
     * @var CancelOrderPaymentInterface
     */
    private $cancelOrderPayment;

    /**
     * @var WaitingOrderPaymentInterface
     */
    private $waitingOrderPayment;

    /**
     * @param AcceptOrderPaymentInterface $acceptOrderPayment
     * @param CancelOrderPaymentInterface $cancelOrderPayment
     * @param WaitingOrderPaymentInterface $waitingOrderPayment
     */
    public function __construct(
        AcceptOrderPaymentInterface $acceptOrderPayment,
        CancelOrderPaymentInterface $cancelOrderPayment,
        WaitingOrderPaymentInterface $waitingOrderPayment
    ) {
        $this->acceptOrderPayment = $acceptOrderPayment;
        $this->cancelOrderPayment = $cancelOrderPayment;
        $this->waitingOrderPayment = $waitingOrderPayment;
    }

    /**
     * Process notify request
     *
     * @param string $status
     * @param string $txnId
     * @param int $totalAmount
     * @param string|null $paymentId
     *
     * @return void
     * @throws CommandException
     */
    public function process($status, $txnId, $totalAmount, $paymentId = null)
    {
        $totalAmount = (float)($totalAmount / 100);
        switch ($status) {
            case \OpenPayuOrderStatus::STATUS_COMPLETED:
                $this->acceptOrderPayment->execute($txnId, $totalAmount, $paymentId);
                break;
            case \OpenPayuOrderStatus::STATUS_CANCELED:
                $this->cancelOrderPayment->execute($txnId, $totalAmount);
                break;
            case \OpenPayuOrderStatus::STATUS_WAITING_FOR_CONFIRMATION:
            case \OpenPayuOrderStatus::STATUS_REJECTED:
                $this->waitingOrderPayment->execute($txnId, $status);
                break;
            case \OpenPayuOrderStatus::STATUS_PENDING:
                break;
            default:
                throw new CommandException(__('Unknown Action'));
        }
    }
}
