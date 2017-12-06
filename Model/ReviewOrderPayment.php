<?php

namespace PayU\PaymentGateway\Model;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderPaymentRepositoryInterface;
use Magento\Sales\Model\Order\Payment;
use PayU\PaymentGateway\Api\OrderPaymentResolverInterface;
use PayU\PaymentGateway\Api\ReviewOrderPaymentInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\OrderRepositoryInterface;
use PayU\PaymentGateway\Api\PayUUpdateOrderStatusInterface;
use Magento\Framework\Event\ManagerInterface as EventManager;

/**
 * Class ReviewOrderPayment
 * @package PayU\PaymentGateway\Model
 */
class ReviewOrderPayment implements ReviewOrderPaymentInterface
{
    /**
     * Error Message
     */
    const REVIEW_ERROR = 'We can\'t update the payment right now.';

    /**
     * @var Payment
     */
    private $payment;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var OrderPaymentRepositoryInterface
     */
    private $orderPaymentRepository;

    /**
     * @var PayUUpdateOrderStatusInterface
     */
    private $updateOrderStatus;

    /**
     * @var EventManager
     */
    private $eventManager;

    /**
     * @var OrderPaymentResolverInterface
     */
    private $paymentResolver;

    /**
     * ReviewOrderPayment constructor.
     *
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderPaymentRepositoryInterface $paymentRepository
     * @param PayUUpdateOrderStatusInterface $updateOrderStatus
     * @param EventManager $eventManager
     * @param OrderPaymentResolverInterface $paymentResolver
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        OrderPaymentRepositoryInterface $paymentRepository,
        PayUUpdateOrderStatusInterface $updateOrderStatus,
        EventManager $eventManager,
        OrderPaymentResolverInterface $paymentResolver
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderPaymentRepository = $paymentRepository;
        $this->updateOrderStatus = $updateOrderStatus;
        $this->eventManager = $eventManager;
        $this->paymentResolver = $paymentResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(OrderInterface $order, $action)
    {
        $this->payment = $order->getPayment();
        $this->payment = $this->paymentResolver->getLast($order);
        $method = $action . 'Payment';
        if (!method_exists($this, $method)) {
            throw new LocalizedException(__('Action "%1" is not supported.', $action));
        }

        $this->{$method}();
        $this->orderRepository->save($this->payment->getOrder());
        $this->orderPaymentRepository->save($this->payment);
    }

    /**
     * Accept Payment Action
     *
     * @return void
     * @throws LocalizedException
     */
    protected function acceptPayment()
    {
        $response = $this->updateOrderStatus->update(
            $this->payment->getMethod(),
            $this->payment->getLastTransId(),
            \OpenPayuOrderStatus::STATUS_COMPLETED
        );
        if ($response->getStatus() !== \OpenPayU_Order::SUCCESS) {
            throw new LocalizedException(__(static::REVIEW_ERROR));
        }
    }

    /**
     * Deny Payment Action
     *
     * @return void
     * @throws LocalizedException
     */
    protected function denyPayment()
    {
        $response = $this->updateOrderStatus->cancel(
            $this->payment->getMethod(),
            $this->payment->getLastTransId()

        );
        if ($response !== null && $response->getStatus() === \OpenPayU_Order::SUCCESS) {
            $this->eventManager->dispatch('payu_deny_payment', ['payment' => $this->payment]);
        } else {
            throw new LocalizedException(__(static::REVIEW_ERROR));
        }
    }
}
