<?php

namespace PayU\PaymentGateway\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Sales\Api\Data\TransactionInterface;
use Magento\Sales\Model\Order\Creditmemo\Comment;

/**
 * Class AuthorizationRequest
 * @package PayU\PaymentGateway\Gateway\Request
 */
class RefundRequest extends AbstractRequest implements BuilderInterface
{
    /**
     * Amount key
     */
    const BUILD_SUBJECT_AMOUNT = 'amount';

    /**
     * {@inheritdoc}
     */
    public function build(array $buildSubject)
    {
        parent::build($buildSubject);
        if (!isset($buildSubject[static::BUILD_SUBJECT_AMOUNT])) {
            throw new \InvalidArgumentException(__('Amount should be provided'));
        }

        return [
            'TXN_ID' => $this->getTxnId(),
            'paymentCode' => $this->payment->getMethodInstance()->getCode(),
            'description' => $this->getLastCreditMemoComment(),
            static::BUILD_SUBJECT_AMOUNT => $buildSubject[static::BUILD_SUBJECT_AMOUNT] * 100
        ];
    }

    /**
     * Get Payment Id from parent transaction
     *
     * @return string
     */
    private function getTxnId()
    {
        return str_replace('-' . TransactionInterface::TYPE_CAPTURE, '', $this->payment->getParentTransactionId());
    }

    /**
     * Return comment for current credit memo
     *
     * @return string
     */
    private function getLastCreditMemoComment()
    {
        $comments = $this->payment->getCreditmemo()->getComments();
        if ($comments) {
            /** @var Comment $comment */
            foreach ($comments as $comment) {
                if ($comment->isObjectNew() && !empty($comment->getComment())) {
                    return $comment->getComment();
                }
            }
        }

        return __('Refund for order %1', $this->order->getOrderIncrementId());
    }
}
