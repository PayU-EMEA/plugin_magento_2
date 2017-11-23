<?php

namespace PayU\PaymentGateway\Model;

use PayU\PaymentGateway\Api\PayUCreateOrderInterface;
use PayU\PaymentGateway\Api\PayUConfigInterface;

/**
 * Class GetPayMethods
 * @package PayU\PaymentGateway\Model
 */
class CreateOrder implements PayUCreateOrderInterface
{
    /**
     * @var \OpenPayU_Order
     */
    private $openPayUOrder;

    /**
     * @var PayUConfigInterface
     */
    private $payUConfig;

    /**
     * GetPayMethods constructor.
     *
     * @param \OpenPayU_Order $openPayUOrder
     * @param PayUConfigInterface $payUConfig
     */
    public function __construct(\OpenPayU_Order $openPayUOrder, PayUConfigInterface $payUConfig)
    {
        $this->openPayUOrder = $openPayUOrder;
        $this->payUConfig = $payUConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($type, array $data = [])
    {
        $this->payUConfig->setDefaultConfig($type);
        $data['merchantPosId'] = $this->payUConfig->getMerchantPosiId();
        $payUOrder = $this->openPayUOrder;
        $response = $payUOrder::create($data)->getResponse();

        return get_object_vars($response);
    }
}
