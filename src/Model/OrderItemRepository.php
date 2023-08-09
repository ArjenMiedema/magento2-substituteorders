<?php

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Model;

use Dealer4Dealer\SubstituteOrders\Api\Data\OrderItemInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\OrderItemSearchResultsInterface;
use Exception;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Dealer4Dealer\SubstituteOrders\Model\ResourceModel\OrderItem as ResourceOrderItem;
use Dealer4Dealer\SubstituteOrders\Api\Data\OrderItemSearchResultsInterfaceFactory;
use Dealer4Dealer\SubstituteOrders\Api\Data\OrderItemInterfaceFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Dealer4Dealer\SubstituteOrders\Model\ResourceModel\OrderItem\CollectionFactory;
use Dealer4Dealer\SubstituteOrders\Api\OrderItemRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;


class OrderItemRepository implements OrderItemRepositoryInterface
{
    public function __construct(
        private readonly ResourceOrderItem $resource,
        private readonly OrderItemFactory $orderItemFactory,
        private readonly CollectionFactory $collectionFactory,
        private readonly OrderItemSearchResultsInterfaceFactory $searchResultFactory,
        private readonly CollectionProcessorInterface $collectionProcessor,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
    }

    /**
     * @throws CouldNotSaveException
     */
    public function save(
        OrderItemInterface $orderItem
    ): OrderItemInterface {
        try {
            $this->resource->save($orderItem);
        } catch (Exception $exception) {
            throw new CouldNotSaveException(
                __(
                    'Could not save the orderItem: %1',
                    $exception->getMessage()
                )
            );
        }

        return $orderItem;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getById(int $orderItemId): OrderItemInterface
    {
        $orderItem = $this->orderItemFactory->create();
        $orderItem->load($orderItemId);
        if (!$orderItem->getId()) {
            throw new NoSuchEntityException(__('OrderItem with id "%1" does not exist.', $orderItemId));
        }

        return $orderItem;
    }

    public function getList(
        SearchCriteriaInterface $searchCriteria
    ): OrderItemSearchResultsInterface {
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResults = $this->searchResultFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());

        return $searchResults;

    }

    public function getOrderItems(int $orderId): array
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('order_id', $orderId, 'eq')->create();
        $results = $this->getList($searchCriteria);

        return $results->getItems();
    }

    /**
     * @throws CouldNotDeleteException
     */
    public function delete(
        OrderItemInterface $orderItem
    ): bool {
        try {
            $this->resource->delete($orderItem);
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(
                __(
                    'Could not delete the OrderItem: %1',
                    $exception->getMessage()
                )
            );
        }

        return true;
    }

    public function deleteById(int $orderItemId): bool
    {
        return $this->delete($this->getById($orderItemId));
    }
}
