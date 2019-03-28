<?php
namespace PayU\PaymentGateway\Controller\Order;

use Magento\Sales\Controller\AbstractController\OrderViewAuthorization;

class OrderViewAuthorizationForGuest extends OrderViewAuthorization
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->request = $request;

        parent::__construct($customerSession, $orderConfig);
    }
    /**
     * {@inheritdoc}
     */
    public function canView(\Magento\Sales\Model\Order $order)
    {
        $requestHash = $this->request->getParam('hash', null);

        $availableStatuses = $this->orderConfig->getVisibleOnFrontStatuses();

        $hash = md5($order->getCustomerEmail() . $order->getId() . $order->getCreatedAt());

        if ($order->getCustomerId() === null
            && $this->customerSession->getCustomerId() === null
            && $requestHash !== null
            && in_array($order->getStatus(), $availableStatuses, true)
            && $requestHash === $hash
        ) {
            return true;
        }

        return parent::canView($order);
    }
}
