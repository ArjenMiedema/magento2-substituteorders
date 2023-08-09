<?php

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Model;

use Dealer4Dealer\SubstituteOrders\Api\Data\InvoiceInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\InvoiceSearchResultsInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\OrderInterface;
use Exception;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResults;
use Magento\Framework\Exception\NoSuchEntityException;
use Dealer4Dealer\SubstituteOrders\Api\Data\InvoiceSearchResultsInterfaceFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Dealer4Dealer\SubstituteOrders\Model\ResourceModel\Invoice\CollectionFactory;
use Dealer4Dealer\SubstituteOrders\Model\ResourceModel\Invoice as ResourceInvoice;
use Dealer4Dealer\SubstituteOrders\Api\Data\InvoiceInterfaceFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Dealer4Dealer\SubstituteOrders\Api\InvoiceRepositoryInterface;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    public function __construct(
        private readonly ResourceInvoice $resource,
        private readonly InvoiceFactory $invoiceFactory,
        private readonly InvoiceSearchResultsInterfaceFactory $searchResultFactory,
        private readonly CollectionProcessorInterface $collectionProcessor,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly CollectionFactory $collectionFactory
    ) {
    }

    /**
     * @throws CouldNotSaveException
     */
    public function save(InvoiceInterface $invoice): InvoiceInterface
    {
        try {
            $this->resource->save($invoice);
        } catch (Exception $exception) {
            throw new CouldNotSaveException(
                __(
                    'Could not save the invoice: %1',
                    $exception->getMessage()
                )
            );
        }

        return $invoice;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getById(int $invoiceId): InvoiceInterface
    {
        $invoice = $this->invoiceFactory->create();
        $invoice->load($invoiceId);
        if (!$invoice->getId()) {
            throw new NoSuchEntityException(__('Invoice with id "%1" does not exist.', $invoiceId));
        }

        return $invoice;
    }

    public function getList(
        SearchCriteriaInterface $searchCriteria
    ): SearchResults {
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResults = $this->searchResultFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());

        return $searchResults;
    }

    /**
     * @throws CouldNotDeleteException
     */
    public function delete(
        InvoiceInterface $invoice
    ): bool {
        try {
            $this->resource->delete($invoice);
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(
                __(
                    'Could not delete the Invoice: %1',
                    $exception->getMessage()
                )
            );
        }

        return true;
    }

    public function deleteById(int $invoiceId): bool
    {
        return $this->delete($this->getById($invoiceId));
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getByMagentoInvoiceId(int $id): InvoiceInterface
    {
        $invoice = $this->invoiceFactory->create();
        $invoice->load($id, "magento_invoice_id");
        if (!$invoice->getId()) {
            throw new NoSuchEntityException(__('Order with id "%1" does not exist.', $id));
        }

        return $invoice;
    }

    public function getInvoicesByOrder(OrderInterface $order): SearchResults
    {
        return $this->getList(
            $this->searchCriteriaBuilder
                ->addFilter(
                    'invoice_id',
                    $order->getInvoiceIds(),
                    'in'
                )
                ->create()
        );
    }
}
