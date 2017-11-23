<?php

namespace PayU\PaymentGateway\Controller\Order;

use Magento\Sales\Model\OrderRepository;
use Magento\Payment\Gateway\Data\Order\OrderAdapterFactory;
use Magento\Sales\Model\Order;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\UrlInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use PayU\PaymentGateway\Api\CreateOrderResolverInterface;
use PayU\PaymentGateway\Api\PayUConfigInterface;
use PayU\PaymentGateway\Api\PayUCreateOrderInterface;
use PayU\PaymentGateway\Gateway\Validator\AbstractResponseValidator;
use PayU\PaymentGateway\Api\PayURepayOrderInterface;

/**
 * Class RepayOrderResolver
 * @package PayU\PaymentGateway\Controller\Order
 */
class RepayOrderResolver
{
    /**
     * Result Success key
     */
    const SUCCESS_FIELD = 'success';

    /**
     * Order ID params
     */
    const ORDER_ID = 'order_id';

    /**
     * @var CreateOrderResolverInterface
     */
    private $createOrderResolver;

    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * @var OrderAdapterFactory
     */
    private $orderAdapterFactory;

    /**
     * @var PayUCreateOrderInterface
     */
    private $payUCreateOrder;

    /**
     * @var PayURepayOrderInterface
     */
    private $payURepayOrder;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * RepayDataResolver constructor.
     *
     * @param CreateOrderResolverInterface $createOrderResolver
     * @param OrderRepository $orderRepository
     * @param OrderAdapterFactory $orderAdapterFactory
     * @param PayUCreateOrderInterface $payUCreateOrder
     * @param PayURepayOrderInterface $payURepayOrder
     * @param CustomerSession $customerSession
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        CreateOrderResolverInterface $createOrderResolver,
        OrderRepository $orderRepository,
        OrderAdapterFactory $orderAdapterFactory,
        PayUCreateOrderInterface $payUCreateOrder,
        PayURepayOrderInterface $payURepayOrder,
        CustomerSession $customerSession,
        UrlInterface $urlBuilder
    ) {
        $this->createOrderResolver = $createOrderResolver;
        $this->orderRepository = $orderRepository;
        $this->orderAdapterFactory = $orderAdapterFactory;
        $this->payUCreateOrder = $payUCreateOrder;
        $this->payURepayOrder = $payURepayOrder;
        $this->customerSession = $customerSession;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Get Result data from repayment
     *
     * @param int $orderId
     * @param string $method
     * @param string $payUMethodType
     * @param string $payUMethod
     *
     * @return array
     * @throws NoSuchEntityException
     * @throws \Exception
     */
    public function resolve($orderId, $method, $payUMethodType, $payUMethod)
    {
        $returnData = [static::SUCCESS_FIELD => false];
        /** @var Order $order */
        $order = $this->orderRepository->get($orderId);
        $createOrderData = $this->getCreateOrderData($order, $payUMethodType, $payUMethod);
        $response = $this->payUCreateOrder->execute($method, $createOrderData);
        if ($this->isSuccessfulTransaction($response)) {
            $this->payURepayOrder->execute($order, $method, $payUMethodType, $payUMethod, $response['orderId']);
            $statusCode = $response[AbstractResponseValidator::VALIDATION_SUBJECT_STATUS]->statusCode;
            if (array_key_exists(PayUCreateOrderInterface::REDIRECT_URI_FIELD, $response) &&
                ($statusCode === \OpenPayU_Order::SUCCESS ||
                    $statusCode === PayUCreateOrderInterface::WARNING_CONTINUE_3_DS)) {
                $returnData[PayUCreateOrderInterface::REDIRECT_URI_FIELD] =
                    $response[PayUCreateOrderInterface::REDIRECT_URI_FIELD];
            } elseif ($statusCode === PayUCreateOrderInterface::WARNING_CONTINUE_CVV) {
                $this->customerSession->setCvvUrl($response[PayUCreateOrderInterface::REDIRECT_URI_FIELD]);
                $returnData[PayUCreateOrderInterface::REDIRECT_URI_FIELD] = $this->urlBuilder->getUrl(
                    'sales/order/continueCvv',
                    [static::ORDER_ID => $orderId]
                );
            } else {
                $returnData[PayUCreateOrderInterface::REDIRECT_URI_FIELD] =
                    $this->urlBuilder->getUrl('sales/order/view', [static::ORDER_ID => $orderId]);
            }
            $returnData[static::SUCCESS_FIELD] = true;
        }

        return $returnData;
    }

    /**
     * @param Order $order
     * @param string $payUMethodType
     * @param string $payUMethod
     *
     * @return array
     */
    private function getCreateOrderData($order, $payUMethodType, $payUMethod)
    {
        $orderAdapter = $this->orderAdapterFactory->create(['order' => $order]);
        $createOrderData = $this->createOrderResolver->resolve(
            $orderAdapter,
            $payUMethodType,
            $payUMethod,
            $order->getGrandTotal(),
            $order->getOrderCurrencyCode(),
            'sales/order/history'
        );
        unset($createOrderData['txn_type']);
        unset($createOrderData[PayUConfigInterface::PAYU_METHOD_CODE]);
        unset($createOrderData[PayUConfigInterface::PAYU_METHOD_TYPE_CODE]);

        return $createOrderData;
    }

    /**
     * Check if response is success
     *
     * @param array $response
     *
     * @return bool
     */
    private function isSuccessfulTransaction(array $response)
    {
        if (array_key_exists(AbstractResponseValidator::VALIDATION_SUBJECT_STATUS, $response) &&
            isset($response[AbstractResponseValidator::VALIDATION_SUBJECT_STATUS]->statusCode)) {
            $statusCode = $response[AbstractResponseValidator::VALIDATION_SUBJECT_STATUS]->statusCode;

            return (in_array(
                $statusCode,
                [
                    \OpenPayU_Order::SUCCESS,
                    PayUCreateOrderInterface::WARNING_CONTINUE_CVV,
                    PayUCreateOrderInterface::WARNING_CONTINUE_3_DS
                ]
            ));
        }

        return false;
    }
}
