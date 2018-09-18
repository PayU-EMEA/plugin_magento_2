<?php

namespace PayU\PaymentGateway\Controller\Data;

use PayU\PaymentGateway\Api\PayUConfigInterface;
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
     * Status canceled
     */
    const STATUS_CANCELED = 'canceled';

    /**
     * Allowed statuses after canceled notification
     */
    const ALLOWED_STATUSES = array(self::STATUS_CANCELED);

    /**
     * @var PayUConfigInterface
     */
    private $payUConfig;

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
     * @param PayUConfigInterface $payUConfig
     */
    public function __construct(
        AcceptOrderPaymentInterface $acceptOrderPayment,
        CancelOrderPaymentInterface $cancelOrderPayment,
        WaitingOrderPaymentInterface $waitingOrderPayment,
        PayUConfigInterface $payUConfig
    ) {
        $this->acceptOrderPayment = $acceptOrderPayment;
        $this->cancelOrderPayment = $cancelOrderPayment;
        $this->waitingOrderPayment = $waitingOrderPayment;
        $this->payUConfig = $payUConfig;
    }

    /**
     * Process notify request
     *
     * @param string $status
     * @param string $txnId
     * @param int $totalAmount
     * @param string $code
     * @param string|null $paymentId
     *
     * @return void
     * @throws CommandException
     */
    public function process($status, $txnId, $totalAmount, $code, $paymentId = null)
    {
        $totalAmount = (float)($totalAmount / 100);
        switch ($status) {
            case \OpenPayuOrderStatus::STATUS_COMPLETED:
                $this->acceptOrderPayment->execute($txnId, $totalAmount, $paymentId);
                break;
            case \OpenPayuOrderStatus::STATUS_CANCELED:
                if (in_array($this->payUConfig->getStatusAfterCanceledNotification($code), static::ALLOWED_STATUSES)) {
                    $this->cancelOrderPayment->execute($txnId, $totalAmount);
                }
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
