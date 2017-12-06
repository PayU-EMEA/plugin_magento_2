<?php

namespace PayU\PaymentGateway\Controller\Data;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Webapi\Exception;
use PayU\PaymentGateway\Api\PayUConfigInterface;
use PayU\PaymentGateway\Model\Ui\CardConfigProvider;
use PayU\PaymentGateway\Model\Ui\ConfigProvider;

/**
 * Class GetNotify
 * @package PayU\PaymentGateway\Controller\Data
 */
class GetNotify extends Action
{
    /**
     * @var \OpenPayU_Order
     */
    private $payUOrder;

    /**
     * @var PayUConfigInterface
     */
    private $payUConfig;

    /**
     * @var NotifyOrderProcessor
     */
    private $notifyOrderProcessor;

    /**
     * @var NotifyRefundProcessor;
     */
    private $notifyRefundProcessor;

    /**
     * @param Context $context
     * @param NotifyOrderProcessor $notifyOrderProcessor
     * @param NotifyRefundProcessor $notifyRefundProcessor
     * @param \OpenPayU_Order $payUOrder
     * @param PayUConfigInterface $payUConfig
     */
    public function __construct(
        Context $context,
        NotifyOrderProcessor $notifyOrderProcessor,
        NotifyRefundProcessor $notifyRefundProcessor,
        \OpenPayU_Order $payUOrder,
        PayUConfigInterface $payUConfig
    ) {
        parent::__construct($context);
        $this->notifyOrderProcessor = $notifyOrderProcessor;
        $this->notifyRefundProcessor = $notifyRefundProcessor;
        $this->payUOrder = $payUOrder;
        $this->payUConfig = $payUConfig;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        try {
            $rawBody = $this->getRawBody();
            $this->initPayUConfig();
            $order = $this->payUOrder;
            $payUResultData = $order::consumeNotification($rawBody);
            /** @var \stdClass $response */
            $response = $payUResultData->getResponse();
            if (isset($response->order)) {
                $orderRetrieved = $response->order;
                $this->notifyOrderProcessor->process(
                    $orderRetrieved->status,
                    $orderRetrieved->orderId,
                    $orderRetrieved->totalAmount,
                    $this->getPaymentId($response)
                );
            }
            if (isset($response->refund)) {
                $refundRetrieved = $response->refund;
                $this->notifyRefundProcessor->process($refundRetrieved->status, $response->extOrderId);
            }
        } catch (\Exception $exception) {
            $result->setHttpResponseCode(Exception::HTTP_INTERNAL_ERROR);
            echo $exception->getMessage();
        }

        return $result;
    }

    /**
     * @param \stdClass $response
     *
     * @return null|string
     */
    private function getPaymentId($response)
    {
        if (isset($response->properties)) {
            foreach ($response->properties as $property) {
                if ($property->name === 'PAYMENT_ID') {
                    return $property->value;
                }
            }
        }

        return null;
    }

    /**
     * Get raw body from Request Content
     *
     * @return string
     */
    private function getRawBody()
    {
        $body = file_get_contents('php://input');
        if (strlen(trim($body)) > 0) {
            return $body;
        }

        return '';
    }

    /**
     * Initialize PayU configuration
     *
     * @return void
     */
    private function initPayUConfig()
    {
        $type = trim(strip_tags($this->getRequest()->getParam('type', '')));
        $store = (int)trim(strip_tags($this->getRequest()->getParam('store', '')));
        $configType = ConfigProvider::CODE;
        if ($type !== null && $type === PayUConfigInterface::PAYU_CC_TRANSFER_KEY) {
            $configType = CardConfigProvider::CODE;
        }
        $this->payUConfig->setDefaultConfig($configType, $store);
    }
}
