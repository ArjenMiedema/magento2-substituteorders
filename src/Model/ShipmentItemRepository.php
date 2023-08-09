<?php

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Model;

use Dealer4Dealer\SubstituteOrders\Api\Data\ShipmentItemInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\ShipmentItemInterfaceFactory;
use Dealer4Dealer\SubstituteOrders\Api\Data\ShipmentItemSearchResultsInterface;
use Dealer4Dealer\SubstituteOrders\Model\ResourceModel\ShipmentItem as ResourceShipmentItem;
use Exception;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Dealer4Dealer\SubstituteOrders\Api\ShipmentItemRepositoryInterface;
use Magento\Framework\Api\SearchResults;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Dealer4Dealer\SubstituteOrders\Model\ResourceModel\ShipmentItem\CollectionFactory;
use Dealer4Dealer\SubstituteOrders\Api\Data\ShipmentItemSearchResultsInterfaceFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Api\SearchCriteriaBuilder;

class ShipmentItemRepository implements ShipmentItemRepositoryInterface
{
    public function __construct(
        private readonly ResourceShipmentItem $resource,
        private readonly ShipmentItemFactory $shipmentItemFactory,
        private readonly CollectionFactory $collectionFactory,
        private readonly ShipmentItemSearchResultsInterfaceFactory $searchResultFactory,
        private readonly CollectionProcessorInterface $collectionProcessor,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
    }

    /**
     * @throws CouldNotSaveException
     */
    public function save(
        ShipmentItemInterface $shipmentItem
    ): ShipmentItemInterface {
        try {
            $this->resource->save($shipmentItem);
        } catch (Exception $exception) {
            throw new CouldNotSaveException(
                __(
                    'Could not save the shipmentItem: %1',
                    $exception->getMessage()
                )
            );
        }

        return $shipmentItem;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getById(int $shipmentItemId): ShipmentItemInterface
    {
        $shipmentItem = $this->shipmentItemFactory->create();
        $shipmentItem->load($shipmentItemId);
        if (!$shipmentItem->getId()) {
            throw new NoSuchEntityException(__('ShipmentItem with id "%1" does not exist.', $shipmentItemId));
        }

        return $shipmentItem;
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
        ShipmentItemInterface $shipmentItem
    ): bool {
        try {
            $this->resource->delete($shipmentItem);
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(
                __(
                    'Could not delete the ShipmentItem: %1',
                    $exception->getMessage()
                )
            );
        }

        return true;
    }

    public function deleteById(int $shipmentItemId): bool
    {
        return $this->delete($this->getById($shipmentItemId));
    }

    public function getShipmentItems(int $id): array
    {
        return $this->getList(
            $this->searchCriteriaBuilder
                ->addFilter(
                    'shipment_id',
                    $id
                )
                ->create()
        )->getItems();
    }
}
