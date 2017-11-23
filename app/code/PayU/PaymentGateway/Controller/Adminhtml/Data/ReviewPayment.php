<?php

namespace PayU\PaymentGateway\Controller\Adminhtml\Data;

use Magento\Framework\Exception\LocalizedException;
use PayU\PaymentGateway\Api\ReviewOrderPaymentInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use PayU\PaymentGateway\Model\Logger\Logger;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\InputException;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class ReviewPayment
 * @package PayU\PaymentGateway\Controller\Data
 */
class ReviewPayment extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Sales::review_payment';

    /**
     * Message for order no longer exists
     */
    const MESSAGE_ORDER_NOT_EXISTS = 'This order no longer exists.';

    /**
     * Order Id key
     */
    const ORDER_ID = 'order_id';

    /**
     * Magento Path for sales order view
     */
    const SALES_ORDER_VIEW_PATH = 'sales/order/view';

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var ReviewOrderPaymentInterface
     */
    private $reviewOrderPayment;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * ReviewPayment constructor.
     *
     * @param Context $context
     * @param Logger $logger
     * @param ReviewOrderPaymentInterface $reviewOrderPayment
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        Context $context,
        Logger $logger,
        ReviewOrderPaymentInterface $reviewOrderPayment,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->logger = $logger;
        $this->reviewOrderPayment = $reviewOrderPayment;
        $this->orderRepository = $orderRepository;
        parent::__construct($context);
    }

    /**
     * Manage payment state
     *
     * Approves a payment that is in "review" state
     *
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $order = $this->initOrder();
        try {
            if ($order) {
                $action = $this->getRequest()->getParam('action', '');
                $this->reviewOrderPayment->execute($order, $action);
                $message = '';
                if ($action === 'accept') {
                    $message = __('The payment has been accepted.');
                }
                if ($action === 'deny') {
                    $message = __('The payment has been denied.');
                }
                if (!empty($message)) {
                    $this->messageManager->addSuccessMessage($message);
                }
            } else {
                $resultRedirect->setPath(static::SALES_ORDER_VIEW_PATH, [static::ORDER_ID => $order->getEntityId()]);

                return $resultRedirect;
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('We can\'t update the payment right now.'));
            $this->logger->critical($e);
        }
        $resultRedirect->setPath(static::SALES_ORDER_VIEW_PATH, [static::ORDER_ID => $order->getEntityId()]);

        return $resultRedirect;
    }

    /**
     * Initialize order model instance
     *
     * @return OrderInterface|false
     */
    private function initOrder()
    {
        $orderId = $this->getRequest()->getParam(static::ORDER_ID);
        try {
            $order = $this->orderRepository->get($orderId);
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__(static::MESSAGE_ORDER_NOT_EXISTS));

            return false;
        } catch (InputException $e) {
            $this->messageManager->addErrorMessage(__(static::MESSAGE_ORDER_NOT_EXISTS));

            return false;
        }

        return $order;
    }
}
