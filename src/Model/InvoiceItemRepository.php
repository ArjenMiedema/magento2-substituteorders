<?php

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Model;

use Dealer4Dealer\SubstituteOrders\Api\Data\InvoiceItemInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\InvoiceItemSearchResultsInterface;
use Dealer4Dealer\SubstituteOrders\Api\InvoiceItemRepositoryInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\InvoiceItemSearchResultsInterfaceFactory;
use Dealer4Dealer\SubstituteOrders\Api\Data\InvoiceItemInterfaceFactory;
use Exception;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Dealer4Dealer\SubstituteOrders\Model\ResourceModel\InvoiceItem as ResourceInvoiceItem;
use Dealer4Dealer\SubstituteOrders\Model\ResourceModel\InvoiceItem\CollectionFactory;

class InvoiceItemRepository implements InvoiceitemRepositoryInterface
{
    public function __construct(
        private readonly ResourceInvoiceItem $resource,
        private readonly InvoiceItemFactory $invoiceItemFactory,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly SortOrderBuilder $sortOrderBuilder,
        private readonly InvoiceItemSearchResultsInterfaceFactory $searchResultFactory,
        private readonly CollectionProcessorInterface $collectionProcessor,
        private readonly CollectionFactory $collectionFactory
    ) {
    }

    /**
     * @throws CouldNotSaveException
     */
    public function save(InvoiceItemInterface $invoiceItem): InvoiceItemInterface
    {
        try {
            $this->resource->save($invoiceItem);
        } catch (Exception $exception) {
            throw new CouldNotSaveException(
                __(
                    'Could not save the invoiceItem: %1',
                    $exception->getMessage()
                )
            );
        }

        return $invoiceItem;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getById(int $invoiceItemId): InvoiceItemInterface
    {
        /** @var InvoiceItemInterface $invoiceItem */
        $invoiceItem = $this->invoiceItemFactory->create();
        $invoiceItem->load($invoiceItemId);

        if (!$invoiceItem->getId()) {
            throw new NoSuchEntityException(__('Invoice_item with id "%1" does not exist.', $invoiceItemId));
        }

        return $invoiceItem;
    }

    public function getList(SearchCriteriaInterface $searchCriteria): InvoiceItemSearchResultsInterface
    {
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
    public function delete(InvoiceItemInterface $invoiceItem): bool
    {
        try {
            $this->resource->delete($invoiceItem);
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(
                __(
                    'Could not delete the Invoice_item: %1',
                    $exception->getMessage()
                )
            );
        }

        return true;
    }

    public function deleteById(int $invoiceItemId): bool
    {
        return $this->delete($this->getById($invoiceItemId));
    }

    public function getInvoiceItems(int $id): array
    {
        $sortOrderOrderIds = $this->sortOrderBuilder
            ->setField('order_id')
            ->setDirection(SortOrder::SORT_DESC)
            ->create();

        $sortOrderItemIds = $this->sortOrderBuilder
            ->setField('invoiceitem_id')
            ->setDirection(SortOrder::SORT_DESC)
            ->create();

        $searchCriteria = $this->searchCriteriaBuilder
            ->addSortOrder($sortOrderOrderIds)
            ->addSortOrder($sortOrderItemIds)
            ->addFilter('invoice_id', $id, 'eq')
            ->create();

        $results = $this->getList($searchCriteria);

        return $results->getItems();
    }
}
