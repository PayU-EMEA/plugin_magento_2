<?php
namespace PayU\PaymentGateway\Observer;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\UrlInterface;
use Magento\Sales\Model\Order\Payment;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\App\Area;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;

/**
 * Class AfterPlaceOrderRepayProcessor
 * @package PayU\PaymentGateway\Observer
 */
class AfterPlaceOrderRepayEmailProcessor
{
    /**
     * Store key
     */
    const STORE = 'store';

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * AfterPlaceOrderRepayProcessor constructor.
     *
     * @param UrlInterface $urlBuilder
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param TransportBuilder $transportBuilder
     */
    public function __construct(
        UrlInterface $urlBuilder,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        TransportBuilder $transportBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->transportBuilder = $transportBuilder;
    }

    /**
     * Send Repay Email
     *
     * @param Payment $payment
     *
     * @return void
     */
    public function process(Payment $payment)
    {
        $order = $payment->getOrder();
        $emailTempVariables = [
            'repay_url' => $this->urlBuilder->getUrl(
                'sales/order/repayview',
                ['order_id' => $payment->getOrder()->getId()]
            ),
            'order' => $order,
            static::STORE => $this->storeManager->getStore()
        ];
        $senderName = $this->scopeConfig->getValue(
            'trans_email/ident_sales/name',
            ScopeInterface::SCOPE_STORE
        );
        $senderEmail = $this->scopeConfig->getValue(
            'trans_email/ident_sales/email',
            ScopeInterface::SCOPE_STORE
        );
        $sender = [
            'name' => $senderName,
            'email' => $senderEmail,
        ];
        $transport = $this->transportBuilder->setTemplateIdentifier('repay_email_template')->setTemplateOptions(
            [
                'area' => Area::AREA_FRONTEND,
                static::STORE => $order->getStoreId()
            ]
        )->setTemplateVars($emailTempVariables)->setFrom($sender)->addTo(
            $order->getCustomerEmail()
        )->setReplyTo($senderEmail)->getTransport();
        $transport->sendMessage();
    }
}
