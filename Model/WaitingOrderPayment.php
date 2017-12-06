<?php

namespace PayU\PaymentGateway\Model;

use Magento\Sales\Api\OrderPaymentRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use PayU\PaymentGateway\Api\OrderPaymentResolverInterface;
use PayU\PaymentGateway\Api\PayUConfigInterface;
use PayU\PaymentGateway\Api\PayUUpdateOrderStatusInterface;
use PayU\PaymentGateway\Api\WaitingOrderPaymentInterface;
use Magento\Sales\Model\Order;
use Magento\Framework\DB\Transaction;
use Magento\Sales\Model\Order\Payment;
use Magento\Framework\Event\ManagerInterface as EventManager;

/**
 * Class WaitingOrderPayment
 * @package PayU\PaymentGateway\Model
 */
class WaitingOrderPayment implements WaitingOrderPaymentInterface
{
    /**
     * @var PayUUpdateOrderStatusInterface
     */
    private $updateOrderStatus;

    /**
     * @var Transaction
     */
    private $transaction;

    /**
     * @var PayUConfigInterface
     */
    private $payUConfig;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var OrderPaymentRepositoryInterface
     */
    private $paymentRepository;

    /**
     * @var OrderPaymentResolverInterface
     */
    private $paymentResolver;

    /**
     * @var EventManager
     */
    private $eventManager;

    /**
     * WaitingOrderPayment constructor.
     *
     * @param Transaction $transaction
     * @param PayUUpdateOrderStatusInterface $updateOrderStatus
     * @param PayUConfigInterface $payUConfig
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderPaymentRepositoryInterface $paymentRepository
     * @param OrderPaymentResolverInterface $paymentResolver
     * @param EventManager $eventManager
     */
    public function __construct(
        Transaction $transaction,
        PayUUpdateOrderStatusInterface $updateOrderStatus,
        PayUConfigInterface $payUConfig,
        OrderRepositoryInterface $orderRepository,
        OrderPaymentRepositoryInterface $paymentRepository,
        OrderPaymentResolverInterface $paymentResolver,
        EventManager $eventManager
    ) {
        $this->updateOrderStatus = $updateOrderStatus;
        $this->transaction = $transaction;
        $this->payUConfig = $payUConfig;
        $this->orderRepository = $orderRepository;
        $this->paymentRepository = $paymentRepository;
        $this->paymentResolver = $paymentResolver;
        $this->eventManager = $eventManager;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($txnId, $payUStatus)
    {
        $payment = $this->paymentResolver->getByTransactionTxnId($txnId);
        if ($this->payUConfig->isRepaymentActive($payment->getMethod())) {
            $this->processWithRepayment($payment, $payUStatus);
        } else {
            $this->proccessWithoutRepayment($payment, $payUStatus);
        }
    }

    /**
     * Process status change with repeyment set
     *
     * @param Payment $payment
     * @param string $payUStatus
     *
     * @return void
     */
    private function processWithRepayment(Payment $payment, $payUStatus)
    {
        $this->cancelRejectedPayUPayment($payment, $payUStatus);
        if ($payUStatus === \OpenPayuOrderStatus::STATUS_WAITING_FOR_CONFIRMATION) {
            if (!$payment->getData('is_active')) {
                $this->updateOrderStatus->cancel($payment->getMethod(), $payment->getLastTransId());
            } else {
                $this->updateOrderStatus->update(
                    $payment->getMethod(),
                    $payment->getLastTransId(),
                    \OpenPayuOrderStatus::STATUS_COMPLETED
                );
                $this->changeTransactionActiveStatus($payment);
            }
        }
    }

    /**
     * Process status change without repeyment set
     *
     * @param Payment $payment
     * @param string $payUStatus
     *
     * @return void
     */
    private function proccessWithoutRepayment(Payment $payment, $payUStatus)
    {
        $this->cancelRejectedPayUPayment($payment, $payUStatus, true);
        if ($payUStatus === \OpenPayuOrderStatus::STATUS_WAITING_FOR_CONFIRMATION) {
            $order = $payment->getOrder();
            $order->setStatus(Order::STATE_PAYMENT_REVIEW)->setState(Order::STATE_PAYMENT_REVIEW);
            $this->orderRepository->save($order);
            $this->changeTransactionActiveStatus($payment);
        }
    }

    /**
     * Cancel rejected payment PayU REST API
     *
     * @param Payment $payment
     * @param string $payUStatus
     * @param bool $changeOrderStatus
     *
     * @return void
     * @throws \OpenPayU_Exception
     */
    private function cancelRejectedPayUPayment(Payment $payment, $payUStatus, $changeOrderStatus = false)
    {
        if ($payUStatus === \OpenPayuOrderStatus::STATUS_REJECTED) {
            $this->updateOrderStatus->cancel($payment->getMethod(), $payment->getLastTransId(), 2);
            if ($changeOrderStatus) {
                $order = $payment->getOrder();
                $order->cancel();
                $this->orderRepository->save($order);
            }
        }
    }

    /**
     * Run event observer
     *
     * @param Payment $payment
     *
     * @return void
     */
    private function changeTransactionActiveStatus(Payment $payment)
    {
        $this->eventManager->dispatch(
            'payu_close_repayment_transaction',
            ['order' => $payment->getOrder(), 'payment' => $payment]
        );
    }
}
