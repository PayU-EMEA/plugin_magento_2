<?php

namespace PayU\PaymentGateway\Controller\Data;

use Magento\Payment\Gateway\Command\CommandException;
use PayU\PaymentGateway\Api\PayUUpdateRefundStatusInterface;
use PayU\PaymentGateway\Model\UpdateRefundStatus;

/**
 * Class NotifyRefundProcessor
 * @package PayU\PaymentGateway\Controller\Data
 */
class NotifyRefundProcessor
{
    /**
     * @var UpdateRefundStatus
     */
    private $updateRefundStatus;

    /**
     * @param UpdateRefundStatus $updateRefundStatus
     */
    public function __construct(UpdateRefundStatus $updateRefundStatus)
    {
        $this->updateRefundStatus = $updateRefundStatus;
    }

    /**
     * Process refund notify request
     *
     * @param string $status
     * @param string $extOrderId
     *
     * @return void
     * @throws CommandException
     */
    public function process($status, $extOrderId)
    {
        switch ($status) {
            case PayUUpdateRefundStatusInterface::STATUS_FINALIZED:
                $this->updateRefundStatus->addSuccessMessage($extOrderId);
                break;
            case PayUUpdateRefundStatusInterface::STATUS_CANCELED:
                $this->updateRefundStatus->cancel($extOrderId);
                break;
            default:
                throw new CommandException(__('Unknown Action'));
        }
    }
}
