<?php

namespace PayU\PaymentGateway\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Invoice;
use Magento\Framework\Phrase;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;

/**
 * Class DataAssignObserver
 * @package PayU\PaymentGateway\Observer
 */
class AcceptOrderPaymentInvoice implements ObserverInterface
{
    /**
     * @var InvoiceSender
     */
    private $invoiceSender;

    /**
     * @var InvoiceRepositoryInterface
     */
    private $invoiceRepository;

    /**
     * AcceptOrderPaymentInvoice constructor.
     *
     * @param InvoiceSender $invoiceSender
     * @param InvoiceRepositoryInterface $invoiceRepository
     */
    public function __construct(InvoiceSender $invoiceSender, InvoiceRepositoryInterface $invoiceRepository)
    {
        $this->invoiceSender = $invoiceSender;
        $this->invoiceRepository = $invoiceRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $observer->getData('payment');
        /** @var Order $order */
        $order = $observer->getData('order');
        $invoice = $this->getInvoice($payment->getOrder());
        if ($invoice !== null) {
            $this->addInvoiceComment($invoice);
            $order->addStatusHistoryComment($this->getInvoiceComment($invoice))->setIsCustomerNotified(
                true
            );
        }
    }

    /**
     * Get last generated invoice
     *
     * @param Order $order
     *
     * @return Invoice|null|\Magento\Framework\DataObject
     */
    private function getInvoice(Order $order)
    {
        if ($order->hasInvoices()) {
            return $order->getInvoiceCollection()->getLastItem();
        }

        return null;
    }

    /**
     * Add invoice comment and send email to customer
     *
     * @param Invoice $invoice
     *
     * @return void
     */
    private function addInvoiceComment(Invoice $invoice)
    {
        $invoice->addComment($this->getInvoiceComment($invoice), true, true);
        $invoice->setEmailSent(true);
        $this->invoiceRepository->save($invoice);
        $this->invoiceSender->send($invoice);
    }

    /**
     * Get translate for invoice comment
     *
     * @param Invoice $invoice
     *
     * @return Phrase
     */
    private function getInvoiceComment(Invoice $invoice)
    {
        return __('Notified customer about invoice %1.', $invoice->getIncrementId());
    }
}
