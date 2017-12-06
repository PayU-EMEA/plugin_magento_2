<?php

namespace PayU\PaymentGateway\Model;

use Magento\Sales\Api\Data\TransactionInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use PayU\PaymentGateway\Api\PayUConfigInterface;
use PayU\PaymentGateway\Api\PayURepayOrderInterface;
use Magento\Sales\Api\Data\TransactionInterfaceFactory;
use Magento\Sales\Api\TransactionRepositoryInterface;
use Magento\Sales\Api\OrderPaymentRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Api\Data\OrderPaymentInterface;

/**
 * Class RepayOrder
 * @package PayU\PaymentGateway\Model
 */
class RepayOrder implements PayURepayOrderInterface
{
    /**
     * @var TransactionInterfaceFactory
     */
    private $transactionFactory;

    /**
     * @var TransactionRepositoryInterface
     */
    private $transactionRepository;

    /**
     * @var OrderPaymentRepositoryInterface
     */
    private $paymentRepository;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * Reorder constructor.
     *
     * @param TransactionInterfaceFactory $transactionFactory
     * @param TransactionRepositoryInterface $transactionRepository
     * @param OrderPaymentRepositoryInterface $paymentRepository
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        TransactionInterfaceFactory $transactionFactory,
        TransactionRepositoryInterface $transactionRepository,
        OrderPaymentRepositoryInterface $paymentRepository,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->transactionFactory = $transactionFactory;
        $this->transactionRepository = $transactionRepository;
        $this->paymentRepository = $paymentRepository;
        $this->orderRepository = $orderRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(OrderInterface $order, $method, $payUMethodType, $payUMethod, $transactionId)
    {
        /** @var Payment $payment */
        $payment = $order->getPayment();
        $newPayment = $this->makeNewPayment(
            $payment,
            $order->getEntityId(),
            $method,
            $payUMethod,
            $payUMethodType,
            $transactionId
        );
        $this->addPaymentToOrder($order, $payment, $transactionId);
        $this->addTransactionToPayment($newPayment, $order->getEntityId(), $transactionId);
    }

    /**
     * @param OrderInterface|Order $order
     * @param Payment $payment
     * @param string $transactionId
     *
     * @return void
     */
    private function addPaymentToOrder(Order $order, Payment $payment, $transactionId)
    {
        $order->setPayment($payment);
        $order->addStatusHistoryComment(
            __(
                'Authorized amount of %1. Transaction ID: "%2"',
                $payment->formatPrice($payment->getAmountAuthorized()),
                $transactionId
            )
        )->setIsCustomerNotified(
            false
        );
        $this->orderRepository->save($order);
    }

    /**
     * @param Payment $payment
     * @param string $orderId
     * @param string $transactionId
     *
     * @return void
     */
    private function addTransactionToPayment(Payment $payment, $orderId, $transactionId)
    {
        $paymentTransaction = $this->transactionFactory->create();
        $paymentTransaction->setOrderId($orderId);
        $paymentTransaction->setPaymentId($payment->getEntityId());
        $paymentTransaction->setTxnId($transactionId);
        $paymentTransaction->setTxnType(TransactionInterface::TYPE_AUTH);
        $paymentTransaction->setIsClosed(0);
        $this->transactionRepository->save($paymentTransaction);
    }

    /**
     * @param Payment $payment
     * @param string $orderId
     * @param string $method
     * @param string $payUMethod
     * @param string $payUMethodType
     * @param string $transactionId
     *
     * @return OrderPaymentInterface|Payment
     */
    private function makeNewPayment(Payment $payment, $orderId, $method, $payUMethod, $payUMethodType, $transactionId)
    {
        /** @var Payment $newPayment */
        $newPayment = $this->paymentRepository->create();
        $newPayment->setMethod($method);
        $newPayment->setParentId($orderId);
        $newPayment->setBaseAmountAuthorized($payment->getBaseAmountAuthorized());
        $newPayment->setBaseShippingAmount($payment->getBaseShippingAmount());
        $newPayment->setShippingAmount($payment->getShippingAmount());
        $newPayment->setAmountAuthorized($payment->getAmountAuthorized());
        $newPayment->setBaseAmountOrdered($payment->getBaseAmountOrdered());
        $newPayment->setAmountOrdered($payment->getAmountOrdered());
        $newPayment->setAdditionalInformation(PayUConfigInterface::PAYU_METHOD_CODE, $payUMethod);
        $newPayment->setAdditionalInformation(PayUConfigInterface::PAYU_METHOD_TYPE_CODE, $payUMethodType);
        $newPayment->setAdditionalInformation('method_title', 'PayU');
        $newPayment->setLastTransId($transactionId);

        return $this->paymentRepository->save($newPayment);
    }
}
