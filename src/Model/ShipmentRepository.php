<?php

/**
 * A Magento 2 module named Dealer4Dealer\SubstituteOrders
 * Copyright (C) 2017 Maikel Martens
 *
 * This file is part of Dealer4Dealer\SubstituteOrders.
 *
 * Dealer4Dealer\SubstituteOrders is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Dealer4Dealer\SubstituteOrders\Model;

use Dealer4Dealer\SubstituteOrders\Api\Data\OrderInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\ShipmentInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\ShipmentSearchResultsInterface;
use Dealer4Dealer\SubstituteOrders\Model\ResourceModel\Shipment\CollectionFactory;
use Exception;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\ShipmentInterfaceFactory;
use Dealer4Dealer\SubstituteOrders\Api\Data\ShipmentSearchResultsInterfaceFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Dealer4Dealer\SubstituteOrders\Model\ResourceModel\Shipment as ResourceShipment;
use Magento\Framework\Exception\CouldNotDeleteException;
use Dealer4Dealer\SubstituteOrders\Api\ShipmentRepositoryInterface;

class ShipmentRepository implements ShipmentRepositoryInterface
{
    public function __construct(
        private readonly ResourceShipment $resource,
        private readonly ShipmentFactory $shipmentFactory,
        private readonly CollectionFactory $collectionFactory,
        private readonly ShipmentSearchResultsInterfaceFactory $searchResultFactory,
        private readonly CollectionProcessorInterface $collectionProcessor,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
    }

    /**
     * @throws CouldNotSaveException
     */
    public function save(
        ShipmentInterface $shipment
    ): ShipmentInterface {
        try {
            $this->resource->save($shipment);
        } catch (Exception $exception) {
            throw new CouldNotSaveException(
                __(
                    'Could not save the shipment: %1',
                    $exception->getMessage()
                )
            );
        }

        return $shipment;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getById(int $shipmentId): ShipmentInterface
    {
        $shipment = $this->shipmentFactory->create();
        $shipment->load($shipmentId);
        if (!$shipment->getId()) {
            throw new NoSuchEntityException(__('Shipment with id "%1" does not exist.', $shipmentId));
        }

        return $shipment;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getByIncrementId(int $incrementId): ShipmentInterface
    {
        $shipment = $this->shipmentFactory->create();
        $shipment->load($incrementId, "increment_id");
        if (!$shipment->getId()) {
            throw new NoSuchEntityException(__('Shipment with increment Id "%1" does not exist.', $incrementId));
        }

        return $shipment;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        SearchCriteriaInterface $searchCriteria
    ): ShipmentSearchResultsInterface {
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
        ShipmentInterface $shipment
    ): bool {
        try {
            $this->resource->delete($shipment);
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(
                __(
                    'Could not delete the Shipment: %1',
                    $exception->getMessage()
                )
            );
        }

        return true;
    }

    public function deleteById(int $shipmentId): bool
    {
        return $this->delete($this->getById($shipmentId));
    }

    public function getShipmentsByOrder(OrderInterface $order): array
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('order_id', $order->getId(), 'eq')->create();
        $results = $this->getList($searchCriteria);

        return $results->getItems();
    }
}
