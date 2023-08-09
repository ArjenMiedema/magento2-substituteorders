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

use Dealer4Dealer\SubstituteOrders\Api\Data\ShipmentInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\ShipmentSearchResultsInterface;
use Dealer4Dealer\SubstituteOrders\Api\ShipmentManagementInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class ShipmentManagement implements ShipmentManagementInterface
{
    public function __construct(
        private readonly ShipmentFactory $shipmentFactory,
        private readonly OrderAddressFactory $addressFactory,
        private readonly AttachmentRepository $attachmentRepository,
        private readonly ShipmentRepository $shipmentRepository
    ) {
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getShipmentById(int $id): ShipmentInterface
    {
        $shipment = $this->shipmentFactory->create()->load($id);

        if (!$shipment->getId()) {
            throw new NoSuchEntityException(__('Shipment with id "%1" does not exist.', $id));
        }

        return $shipment;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getShipmentByExt(int $id): ShipmentInterface
    {
        $shipment = $this->shipmentFactory->create()->load($id, "ext_shipment_id");

        if (!$shipment->getId()) {
            throw new NoSuchEntityException(__('Shipment with ext_shipment_id "%1" does not exist.', $id));
        }

        return $shipment;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getShipmentByMagentoIncrement(int $id): ShipmentInterface
    {
        $shipment = $this->shipmentFactory->create()->load($id, "increment_id");

        if (!$shipment->getId()) {
            throw new NoSuchEntityException(__('Shipment with increment_id "%1" does not exist.', $id));
        }

        return $shipment;
    }

    public function postShipment(ShipmentInterface $shipment): int
    {
        $shipment->setId(null);
        $shipment->save();

        $this->saveAttachment($shipment);

        return (int) $shipment->getId();
    }

    public function putShipment(ShipmentInterface $shipment): ?int
    {
        /** @var $oldShipment ShipmentInterface */

        if ($shipment->getId()) {
            $oldShipment = $this->shipmentFactory->create()->load($shipment->getId());
        } elseif ($shipment->getIncrementId()) {
            $oldShipment = $this->shipmentFactory->create()->load($shipment->getIncrementId(), "increment_id");
        }

        if (!$oldShipment->getId()) {
            return null;
        }

        $oldShipment->setData(array_merge($oldShipment->getData(), $shipment->getData()));
        if ($shipment->getShippingAddress()) {
            $oldShipment->setShippingAddress($shipment->getShippingAddress());
        }

        if ($shipment->getBillingAddress()) {
            $oldShipment->setBillingAddress($shipment->getBillingAddress());
        }

        $oldShipment->setItems($shipment->getItems());
        $oldShipment->setTracking($shipment->getTracking());
        $oldShipment->setAdditionalData($shipment->getAdditionalData());

        $oldShipment->save();

        $this->saveAttachment($oldShipment);

        return (int) $oldShipment->getId();
    }

    /**
     * @throws NoSuchEntityException
     */
    public function deleteShipmentById(int $id): bool
    {
        $shipment = $this->shipmentFactory->create()->load($id);

        if (!$shipment->getId()) {
            throw new NoSuchEntityException(__('Shipment with id "%1" does not exist.', $id));
        }

        $shipment->delete();

        return true;
    }

    public function saveAttachment(ShipmentInterface $shipment): void
    {
        if (!empty($shipment->getFileContent())) {
            $this->attachmentRepository->saveAttachmentByEntityType(
                Shipment::ENTITY,
                $shipment->getShipmentId(),
                $shipment->getMagentoCustomerId(),
                $shipment->getFileContent()
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        SearchCriteriaInterface $searchCriteria
    ): ShipmentSearchResultsInterface {
        return $this->shipmentRepository->getList($searchCriteria);
    }
}
