<?php

namespace PayU\PaymentGateway\Controller\Account;

use Magento\Customer\Controller\AbstractAccount;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use PayU\PaymentGateway\Api\PayUDeleteUserTokenInterface;
use Magento\Payment\Gateway\Config\Config as GatewayConfig;
use PayU\PaymentGateway\Model\Ui\CardConfigProvider;

/**
 * Class DeleteStoredCard
 * @package PayU\PaymentGateway\Controller\Account
 */
class DeleteStoredCard extends AbstractAccount
{
    /**
     * @var PayUDeleteUserTokenInterface
     */
    private $deleteUserToken;

    /**
     * @var GatewayConfig
     */
    private $gatewayConfig;

    /**
     * @param Context $context
     * @param PayUDeleteUserTokenInterface $deleteUserToken
     * @param GatewayConfig $gatewayConfig
     */
    public function __construct(
        Context $context,
        PayUDeleteUserTokenInterface $deleteUserToken,
        GatewayConfig $gatewayConfig
    ) {
        $this->deleteUserToken = $deleteUserToken;
        $this->gatewayConfig = $gatewayConfig;
        parent::__construct($context);
    }

    /**
     * (@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create()->setPath('*/*/storedcards');
        $cardToken = $this->getRequest()->getParam('card_id');
        if ($cardToken === null) {
            $this->messageManager->addErrorMessage(__('Wrong request.'));
        }
        try {
            $this->gatewayConfig->setMethodCode(CardConfigProvider::CODE);
            $this->deleteUserToken->execute($cardToken);
            $this->messageManager->addSuccessMessage(__('Card token was successfully deleted.'));
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        }

        return $resultRedirect;
    }
}
