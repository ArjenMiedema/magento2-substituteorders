<?php

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Model;

use Dealer4Dealer\SubstituteOrders\Api\Data\InvoiceInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\InvoiceSearchResultsInterface;
use Dealer4Dealer\SubstituteOrders\Api\InvoiceManagementInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResults;
use Magento\Framework\Data\SearchResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class InvoiceManagement implements InvoiceManagementInterface
{
    public function __construct(
        private readonly InvoiceFactory $invoiceFactory,
        private readonly OrderFactory $orderFactory,
        private readonly AttachmentRepository $attachmentRepository,
        private readonly InvoiceRepository $invoiceRepository
    ) {
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getInvoice(int $id): InvoiceInterface
    {
        $invoice = $this->invoiceFactory->create()->load($id);

        if (!$invoice->getId()) {
            throw new NoSuchEntityException(__('Invoice with id "%1" does not exist.', $id));
        }

        return $invoice;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getInvoiceByExt(int $id): InvoiceInterface
    {
        $invoice = $this->invoiceFactory->create()->load($id, "ext_invoice_id");

        if (!$invoice->getId()) {
            throw new NoSuchEntityException(__('Invoice with ext_invoice_id "%1" does not exist.', $id));
        }

        return $invoice;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getInvoiceByMagento(int $id): InvoiceInterface
    {
        $invoice = $this->invoiceFactory->create()->load($id, "magento_invoice_id");

        if (!$invoice->getId()) {
            throw new NoSuchEntityException(__('Invoice with magento_invoice_id "%1" does not exist.', $id));
        }

        return $invoice;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getInvoiceByMagentoIncrementId(int $id): InvoiceInterface
    {
        $invoice = $this->invoiceFactory->create()->load($id, "magento_increment_id");

        if (!$invoice->getId()) {
            throw new NoSuchEntityException(__('Invoice with magento_increment_id "%1" does not exist.', $id));
        }

        return $invoice;
    }

    public function postInvoice(InvoiceInterface $invoice): int
    {
        $invoice->setId(null);
        $invoice->save();

        $this->saveAttachment($invoice);

        return (int) $invoice->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function putInvoice(InvoiceInterface $invoice): ?int
    {
        $oldInvoice = $this->invoiceFactory->create()->load($invoice->getId());

        if (!$oldInvoice->getId()) {
            return null;
        }

        $oldInvoice->setData(array_merge($oldInvoice->getData(), $invoice->getData()));
        $oldInvoice->setShippingAddress($invoice->getShippingAddress());
        $oldInvoice->setBillingAddress($invoice->getBillingAddress());
        $oldInvoice->setItems($invoice->getItems());
        $oldInvoice->setAdditionalData($invoice->getAdditionalData());

        $oldInvoice->save();

        $this->saveAttachment($oldInvoice);

        return (int) $oldInvoice->getId();
    }

    /**
     * @throws NoSuchEntityException
     */
    public function deleteInvoice(int $id): bool
    {
        $invoice = $this->invoiceFactory->create()->load($id);

        if (!$invoice->getId()) {
            throw new NoSuchEntityException(__('Order with id "%1" does not exist.', $id));
        }

        $invoice->delete();

        return true;
    }

    public function saveAttachment(InvoiceInterface $invoice): void
    {
        if (!empty($invoice->getFileContent())) {
            $this->attachmentRepository->saveAttachmentByEntityType(
                Invoice::ENTITY,
                $invoice->getInvoiceId(),
                $invoice->getMagentoCustomerId(),
                $invoice->getFileContent()
            );
        }
    }

    public function getList(
        SearchCriteriaInterface $searchCriteria
    ): InvoiceSearchResultsInterface {
        return $this->invoiceRepository->getList($searchCriteria);
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getInvoicesByOrderIncrementId(int $id): InvoiceSearchResultsInterface
    {
        // 1. get order by increment id.
        $order = $this->orderFactory->create();
        $order->load($id, "magento_increment_id");
        if (!$order->getId()) {
            throw new NoSuchEntityException(__('Order with increment_id "%1" does not exist.', $id));
        }

        // 2. get shipments.
        return $this->invoiceRepository->getInvoicesByOrder($order);
    }
}
