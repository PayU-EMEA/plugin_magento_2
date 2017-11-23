<?php

namespace PayU\PaymentGateway\Controller\Order;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Controller\Result\JsonFactory;
use PayU\PaymentGateway\Api\RepaymentResolverInterface;
use PayU\PaymentGateway\Model\Logger\Logger;

/**
 * Class Repay
 * @package PayU\PaymentGateway\Controller\Order
 */
class Repay extends Action
{
    /**
     * Order ID param
     */
    const ORDER_ID = 'order_id';

    /**
     * Result Success key
     */
    const SUCCESS_FIELD = 'success';

    /**
     * Result Error key
     */
    const ERROR_FIELD = 'error';

    /**
     * Error message
     */
    const ERROR_MESASGE = 'Can\'t repay order.';

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var RepayOrderResolver
     */
    private $repayOrderResolver;

    /**
     * @var RepaymentResolverInterface
     */
    private $repaymentResolver;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param Logger $logger
     * @param RepayOrderResolver $repayOrderResolver
     * @param RepaymentResolverInterface $repaymentResolver
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        Logger $logger,
        RepayOrderResolver $repayOrderResolver,
        RepaymentResolverInterface $repaymentResolver
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->logger = $logger;
        $this->repayOrderResolver = $repayOrderResolver;
        $this->repaymentResolver = $repaymentResolver;
        parent::__construct($context);
    }

    /**
     * (@inheritdoc}
     */
    public function execute()
    {
        $isRepayment = $this->repaymentResolver->isRepayment((int)$this->getRequest()->getParam(static::ORDER_ID));
        if (!$isRepayment) {
            return $this->resultRedirectFactory->create()->setPath('sales/order/history');
        }
        $returnData = [static::SUCCESS_FIELD => false];
        $orderId = (int)$this->getRequest()->getParam(static::ORDER_ID);
        $method = strip_tags(trim($this->getRequest()->getParam('method', '')));
        $payUMethod = strip_tags(trim($this->getRequest()->getParam('payu_method', '')));
        $payUMethodType = strip_tags(trim($this->getRequest()->getParam('payu_method_type', '')));
        if ($orderId === null) {
            $returnData[static::ERROR_FIELD] = __('Wrong Request');
        }
        try {
            $returnData = $this->repayOrderResolver->resolve($orderId, $method, $payUMethodType, $payUMethod);
        } catch (NoSuchEntityException $exception) {
            $this->logger->critical($exception->getMessage());
            $returnData[static::ERROR_FIELD] = __(static::ERROR_MESASGE);
        } catch (\Exception $exception) {
            $this->logger->critical($exception->getMessage());
            $returnData[static::ERROR_FIELD] = __(static::ERROR_MESASGE);
        }

        return $this->resultJsonFactory->create()->setData($returnData);
    }
}
