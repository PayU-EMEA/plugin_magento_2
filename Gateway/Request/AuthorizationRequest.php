<?php

namespace PayU\PaymentGateway\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use PayU\PaymentGateway\Api\CreateOrderResolverInterface;
use PayU\PaymentGateway\Api\PayUConfigInterface;

/**
 * Class AuthorizationRequest
 * @package PayU\PaymentGateway\Gateway\Request
 */
class AuthorizationRequest extends AbstractRequest implements BuilderInterface
{
    /**
     * @var CreateOrderResolverInterface
     */
    private $createOrderResolver;

    /**
     * @param CreateOrderResolverInterface $createOrderResolver
     */
    public function __construct(CreateOrderResolverInterface $createOrderResolver)
    {
        $this->createOrderResolver = $createOrderResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function build(array $buildSubject)
    {
        parent::build($buildSubject);

        return $this->createOrderResolver->resolve(
            $this->order,
            $this->payment->getAdditionalInformation(
                PayUConfigInterface::PAYU_METHOD_TYPE_CODE
            ),
            $this->payment->getAdditionalInformation(
                PayUConfigInterface::PAYU_METHOD_CODE
            )
        );
    }
}
