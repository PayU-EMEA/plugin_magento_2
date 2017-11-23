<?php

namespace PayU\PaymentGateway\Model;

use Magento\Payment\Gateway\Command\CommandException;
use PayU\PaymentGateway\Api\AcceptOrderPaymentInterface;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\DB\Transaction;
use PayU\PaymentGateway\Api\OrderPaymentResolverInterface;

/**
 * Class NotifyOrderResolver
 * @package PayU\PaymentGateway\Model
 */
class AcceptOrderPayment implements AcceptOrderPaymentInterface
{
    /**
     * @var EventManager
     */
    private $eventManager;

    /**
     * @var Transaction
     */
    private $transaction;

    /**
     * @var OrderPaymentResolverInterface
     */
    private $paymentResolver;

    /**
     * AcceptOrderPayment constructor.
     *
     * @param EventManager $eventManager
     * @param Transaction $transaction
     * @param OrderPaymentResolverInterface $paymentResolver
     */
    public function __construct(
        EventManager $eventManager,
        Transaction $transaction,
        OrderPaymentResolverInterface $paymentResolver
    ) {
        $this->eventManager = $eventManager;
        $this->transaction = $transaction;
        $this->paymentResolver = $paymentResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($txnId, $amount, $paymentId = null)
    {
        $payment = $this->paymentResolver->getByTransactionTxnId($txnId);
        if ($payment === null) {
            throw new CommandException(__('Payment does not exist'));
        }
        if (!$payment->canCapture()) {
            throw new CommandException(__('Payment could not be captured'));
        }
        if ($paymentId !== null) {
            $payment->setTransactionAdditionalInfo('payment_id', $paymentId);
        }
        $payment->capture();
        $order = $payment->getOrder();
        $eventData = ['order' => $order, 'payment' => $payment];
        $this->eventManager->dispatch('payu_accept_order_payment', $eventData);
        $this->eventManager->dispatch('payu_close_repayment_transaction', $eventData);
        $this->transaction->addObject($payment);
        $this->transaction->addObject($order);
        $this->transaction->save();
    }
}
