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

use Dealer4Dealer\SubstituteOrders\Api\Data\ExternalOrderInterface;
use Dealer4Dealer\SubstituteOrders\Api\OrderManagementInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\OrderFactory;

class OrderManagement implements OrderManagementInterface
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly OrderFactory $orderFactory
    ) {
    }

    /**
     * @throws AlreadyExistsException
     */
    public function create(ExternalOrderInterface $externalOrder): int
    {
        if ($externalOrder->getId()) {
            throw new AlreadyExistsException(
                __(
                    'An order with the given ID already exists. Please use PUT to update the order data.'
                )
            );
        }

        $order = $this->transformExternalOrder($externalOrder);
        $this->orderRepository->save($order);

        return $order->getId();
    }

    public function get(string $id): ExternalOrderInterface
    {
        var_dump($this->orderRepository->get($id));
    }

    private function transformExternalOrder(ExternalOrderInterface $externalOrder): OrderInterface
    {
        $order = $this->orderFactory->create();

    }

    public function update(ExternalOrderInterface $externalOrder): int
    {

    }

    public function getById(int $id): ExternalOrderInterface
    {

    }

    public function deleteOrderById(int $id): void
    {

    }

    public function getList(
        SearchCriteriaInterface $searchCriteria
    ): OrderSearchResultInterface {

    }
}
