<?php

namespace PayU\PaymentGateway\Controller\Account;

use Magento\Customer\Controller\AbstractAccount;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class StoredCards
 * @package PayU\PaymentGateway\Controller\Account
 */
class StoredCards extends AbstractAccount
{
    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(Context $context, PageFactory $resultPageFactory)
    {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * (@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('My Stored Cards'));

        return $resultPage;
    }

}
