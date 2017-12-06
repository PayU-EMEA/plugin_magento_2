<?php

namespace PayU\PaymentGateway\Controller\Order;

use Magento\Framework\View\Result\PageFactory;
use Magento\Sales\Controller\AbstractController\OrderLoaderInterface;
use Magento\Sales\Controller\AbstractController\View;
use Magento\Framework\App\Action\Context;
use PayU\PaymentGateway\Api\RepaymentResolverInterface;

/**
 * Class RepayView
 * @package PayU\PaymentGateway\Controller\Order
 */
class RepayView extends View
{
    /**
     * @var RepaymentResolverInterface
     */
    private $repaymentResolver;

    /**
     * RepayView constructor.
     *
     * @param Context $context
     * @param OrderLoaderInterface $orderLoader
     * @param PageFactory $resultPageFactory
     * @param RepaymentResolverInterface $repaymentResolver
     */
    public function __construct(
        Context $context,
        OrderLoaderInterface $orderLoader,
        PageFactory $resultPageFactory,
        RepaymentResolverInterface $repaymentResolver
    ) {
        $this->repaymentResolver = $repaymentResolver;
        parent::__construct($context, $orderLoader, $resultPageFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $isRepayment = $this->repaymentResolver->isRepayment((int)$this->getRequest()->getParam('order_id'));
        if (!$isRepayment) {
            return $this->resultRedirectFactory->create()->setPath('sales/order/history');
        }

        return parent::execute();
    }
}
