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
use Dealer4Dealer\SubstituteOrders\Api\Data\OrderSearchResultsInterface;
use Dealer4Dealer\SubstituteOrders\Api\OrderManagementInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResults;
use Magento\Framework\Exception\NoSuchEntityException;

use function Dealer4Dealer\SubstituteOrders\Model\__;

class OrderManagement implements OrderManagementInterface
{
    public function __construct(
        private readonly \Dealer4Dealer\SubstituteOrders\Model\OrderFactory $orderFactory,
        private readonly AttachmentRepository $attachmentRepository,
        private readonly OrderRepository $orderRepository,
        private readonly OrderItemRepository $orderItemRepository
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function postOrder(OrderInterface $order): int
    {
        $order->setId(null);
        $order->save();

        $this->saveAttachment($order);

        return $order->getId();
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getOrderById(int $id): OrderInterface
    {
        $order = $this->orderFactory->create()->load($id);

        if (!$order->getId()) {
            throw new NoSuchEntityException(__('Order with id "%1" does not exist.', $id));
        }

        return $order;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getOrderByMagento(int $id): OrderInterface
    {
        $order = $this->orderFactory->create()->load($id, "magento_order_id");

        if (!$order->getId()) {
            throw new NoSuchEntityException(__('Order with magento_order_id "%1" does not exist.', $id));
        }

        return $order;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getOrderByMagentoIncrementId(int $id): OrderInterface
    {
        $order = $this->orderFactory->create()->load($id, "magento_increment_id");

        if (!$order->getId()) {
            throw new NoSuchEntityException(__('Order with magento_increment_id "%1" does not exist.', $id));
        }

        return $order;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getOrderByExt(int $id): OrderInterface
    {
        $order = $this->orderFactory->create()->load($id, "ext_order_id");

        if (!$order->getId()) {
            throw new NoSuchEntityException(__('Order with ext_order_id "%1" does not exist.', $id));
        }

        return $order;
    }

    public function putOrder(OrderInterface $order): int
    {
        $oldOrder = $this->orderFactory->create()->load($order->getId());

        if (!$oldOrder->getId()) {
            return false;
        }

        $oldOrder->setData(array_merge($oldOrder->getData(), $order->getData()));

        if ($shippingAddress = $order->getShippingAddress()) {
            $oldOrder->setShippingAddress($shippingAddress);
        }

        if ($billingAddress = $order->getBillingAddress()) {
            $oldOrder->setBillingAddress($billingAddress);
        }

        foreach ($oldOrder->getItems() as $oldOrderItem) {
            $this->orderItemRepository->delete($oldOrderItem);
        }

        $oldOrder->setItems($order->getItems());
        $oldOrder->setAdditionalData($order->getAdditionalData());

        $oldOrder->save();

        $this->saveAttachment($oldOrder);

        return $oldOrder->getId();
    }

    /**
     * @throws NoSuchEntityException
     */
    public function deleteOrderById(int $id): bool
    {
        $order = $this->orderFactory->create()->load($id);

        if (!$order->getId()) {
            throw new NoSuchEntityException(__('Order with id "%1" does not exist.', $id));
        }

        $order->delete();

        return true;
    }

    public function saveAttachment(OrderInterface $order): void
    {
        if (!empty($order->getFileContent())) {
            $this->attachmentRepository->saveAttachmentByEntityType(
                Order::ENTITY,
                $order->getOrderId(),
                $order->getMagentoCustomerId(),
                $order->getFileContent()
            );
        }
    }

    public function getList(
        SearchCriteriaInterface $searchCriteria
    ): SearchResults {
        return $this->orderRepository->getList($searchCriteria);
    }
}
