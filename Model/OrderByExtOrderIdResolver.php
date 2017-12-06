<?php

namespace PayU\PaymentGateway\Model;

use PayU\PaymentGateway\Api\OrderByExtOrderIdResolverInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class OrderByExtOrderIdResolver
 * @package PayU\PaymentGateway\Model
 */
class OrderByExtOrderIdResolver implements OrderByExtOrderIdResolverInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    private $order;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * AcceptOrderPayment constructor.
     *
     * @param OrderRepositoryInterface $order
     * @param SearchCriteriaBuilder $searchCriteria
     */
    public function __construct(OrderRepositoryInterface $order, SearchCriteriaBuilder $searchCriteria)
    {
        $this->order = $order;
        $this->searchCriteriaBuilder = $searchCriteria;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve($extOrderId)
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter(
            'increment_id',
            $this->getIncrementIdFromExtOrderId($extOrderId),
            'eq'
        )->create();
        $orderList = $this->order->getList($searchCriteria)->getItems();

        return current($orderList);
    }

    /**
     * Get Increment ID from External Order ID. Due to external order ID format is incrementId_number method split
     * string into increment ID and number of transaction per increment ID
     *
     * @param string $extOrderId
     *
     * @return string
     */
    private function getIncrementIdFromExtOrderId($extOrderId)
    {
        list($incrementId, $noTransaction) = explode('_', $extOrderId);
        unset($noTransaction);

        return $incrementId;
    }

}
