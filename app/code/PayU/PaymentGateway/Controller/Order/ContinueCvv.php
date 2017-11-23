<?php

namespace PayU\PaymentGateway\Controller\Order;

use Magento\Framework\View\Result\PageFactory;
use Magento\Sales\Controller\AbstractController\OrderLoaderInterface;
use Magento\Sales\Controller\AbstractController\View;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session as CustomerSession;

/**
 * Class ContinueCvv
 * @package PayU\PaymentGateway\Controller\Onepage
 */
class ContinueCvv extends View
{
    /**
     * Order ID key
     */
    const ORDER_ID = 'order_id';

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * ContinueCvv constructor.
     *
     * @param Context $context
     * @param OrderLoaderInterface $orderLoader
     * @param PageFactory $resultPageFactory
     * @param CustomerSession $customerSession
     */
    public function __construct(
        Context $context,
        OrderLoaderInterface $orderLoader,
        PageFactory $resultPageFactory,
        CustomerSession $customerSession
    ) {
        $this->customerSession = $customerSession;
        parent::__construct($context, $orderLoader, $resultPageFactory);
    }

    /**
     * (@inheritdoc}
     */
    public function execute()
    {
        if ($this->customerSession->getCvvUrl() === null) {
            $orderId = (int)$this->getRequest()->getParam(static::ORDER_ID);

            return $this->resultRedirectFactory->create()->setPath(
                'sales/order/repaywiev',
                [static::ORDER_ID => $orderId]
            );
        }

        return parent::execute();
    }
}
