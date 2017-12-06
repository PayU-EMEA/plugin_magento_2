<?php

namespace PayU\PaymentGateway\Controller\Onepage;

use Magento\Framework\App\Action\Action;
use Magento\Checkout\Model\Session\SuccessValidator;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Phrase;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session as CustomerSession;

/**
 * Class ContinueCvv
 * @package PayU\PaymentGateway\Controller\Onepage
 */
class ContinueCvv extends Action
{
    /**
     * @var SuccessValidator
     */
    private $successValidator;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @param Context $context
     * @param SuccessValidator $successValidator
     * @param PageFactory $resultPageFactory
     * @param CustomerSession $customerSession
     */
    public function __construct(
        Context $context,
        SuccessValidator $successValidator,
        PageFactory $resultPageFactory,
        CustomerSession $customerSession
    ) {
        $this->successValidator = $successValidator;
        $this->resultPageFactory = $resultPageFactory;
        $this->customerSession = $customerSession;
        parent::__construct($context);
    }

    /**
     * Payu Warning Continue CVV Action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            if (!$this->successValidator->isValid()) {
                throw new ValidatorException(new Phrase('Session Validation Error'));
            }

            if ($this->customerSession->getCvvUrl() === null) {
                throw new ValidatorException(new Phrase('CVV Validation Error'));
            }

            return $this->resultPageFactory->create();
        } catch (\Exception $exception) {
            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        }
    }
}
