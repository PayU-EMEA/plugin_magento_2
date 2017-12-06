<?php

namespace PayU\PaymentGateway\Model;

use PayU\PaymentGateway\Api\GetAvailableLocaleInterface;
use Magento\Framework\Locale\ResolverInterface;

/**
 * Class GetAvailableLocale
 * @package PayU\PaymentGateway\Model
 */
class GetAvailableLocale implements GetAvailableLocaleInterface
{
    /**
     * @var ResolverInterface
     */
    private $resolver;

    /**
     * GetAvailableLocale constructor.
     *
     * @param ResolverInterface $resolver
     */
    public function __construct(ResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(array $availableLanguages = [])
    {
        $currentLocale = current(explode('_', $this->resolver->getLocale()));
        if (empty($availableLanguages) || in_array($currentLocale, $availableLanguages)) {

            return current(explode('_', $this->resolver->getLocale()));
        }

        return 'en';
    }
}
