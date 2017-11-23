<?php

namespace PayU\PaymentGateway\Controller\Data;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Json\EncoderInterface;
use PayU\PaymentGateway\Api\PayUGetCreditCardWidgetConfigInterface;

/**
 * Class GetCardWidgetConfig
 */
class GetCardWidgetConfig extends Action
{
    /**
     * Result Success key
     */
    const SUCCESS_FIELD = 'success';

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var PayUGetCreditCardWidgetConfigInterface
     */
    private $cardWidgetConfig;

    /**
     * @var EncoderInterface
     */
    private $encoder;

    /**
     * GetRedirectUrl constructor.
     *
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param PayUGetCreditCardWidgetConfigInterface $cardWidgetConfig
     * @param EncoderInterface $encoder
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        PayUGetCreditCardWidgetConfigInterface $cardWidgetConfig,
        EncoderInterface $encoder
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->cardWidgetConfig = $cardWidgetConfig;
        $this->encoder = $encoder;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $returnData = [static::SUCCESS_FIELD => false];
        try {
            $email = $this->getRequest()->getParam('email');
            if ($email !== null) {
                $returnData = [
                    static::SUCCESS_FIELD => true,
                    'widgetConfig' => $this->encoder->encode($this->cardWidgetConfig->execute($email)),
                ];
            }
        } catch (\Exception $exception) {
            $returnData['message'] = $exception->getMessage();
        }

        return $this->resultJsonFactory->create()->setData($returnData);
    }
}

