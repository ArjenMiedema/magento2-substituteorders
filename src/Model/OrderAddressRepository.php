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

use Dealer4Dealer\SubstituteOrders\Api\Data\OrderAddressInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\OrderAddressInterfaceFactory;
use Dealer4Dealer\SubstituteOrders\Api\Data\OrderAddressSearchResultsInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Dealer4Dealer\SubstituteOrders\Model\ResourceModel\OrderAddress as ResourceOrderAddress;
use Dealer4Dealer\SubstituteOrders\Api\Data\OrderAddressSearchResultsInterfaceFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Dealer4Dealer\SubstituteOrders\Model\ResourceModel\OrderAddress\CollectionFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Dealer4Dealer\SubstituteOrders\Api\OrderAddressRepositoryInterface;
use Magento\Sales\Api\Data\OrderAddressInterface as MagentoOrderAddressInterface;

class OrderAddressRepository implements OrderAddressRepositoryInterface
{
    public function __construct(
        private readonly ResourceOrderAddress $resource,
        private readonly OrderAddressFactory $orderAddressFactory,
        private readonly OrderAddressSearchResultsInterfaceFactory $searchResultFactory,
        private readonly CollectionProcessorInterface $collectionProcessor,
        private readonly CollectionFactory $collectionFactory
    ) {
    }

    /**
     * @throws CouldNotSaveException
     */
    public function save(
        OrderAddressInterface $orderAddress
    ): OrderAddressInterface {
        try {
            $this->resource->save($orderAddress);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __(
                    'Could not save the orderAddress: %1',
                    $exception->getMessage()
                )
            );
        }

        return $orderAddress;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getById(int $orderAddressId): OrderAddressInterface
    {
        $orderAddress = $this->orderAddressFactory->create();
        $orderAddress->load($orderAddressId);
        if (!$orderAddress->getId()) {
            throw new NoSuchEntityException(__('OrderAddress with id "%1" does not exist.', $orderAddressId));
        }

        return $orderAddress;
    }

    public function saveByAddress(
        MagentoOrderAddressInterface $address
    ): OrderAddressInterface {
        $orderAddress = $this->orderAddressFactory->create();
        $orderAddress->setName(
            implode(
                " ",
                array_filter(
                    [
                        $address->getFirstname(),
                        $address->getMiddlename(),
                        $address->getLastname(),
                    ]
                )
            )
        );
        $orderAddress->setCompany($address->getCompany());

        return $this->save($orderAddress);
    }

    public function getList(
        SearchCriteriaInterface $searchCriteria
    ): OrderAddressSearchResultsInterface {
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
        OrderAddressInterface $orderAddress
    ): bool {
        try {
            $this->resource->delete($orderAddress);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(
                __(
                    'Could not delete the OrderAddress: %1',
                    $exception->getMessage()
                )
            );
        }

        return true;
    }

    public function deleteById(int $orderAddressId): bool
    {
        return $this->delete($this->getById($orderAddressId));
    }
}
