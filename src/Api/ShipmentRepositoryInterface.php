<?php

namespace Dealer4Dealer\SubstituteOrders\Api;

use Dealer4Dealer\SubstituteOrders\Api\Data\OrderInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\ShipmentInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\ShipmentSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface ShipmentRepositoryInterface
{
    public function save(ShipmentInterface $shipment): ShipmentInterface;

    public function getById(int $shipmentId): ShipmentInterface;

    public function getList(SearchCriteriaInterface $searchCriteria): ShipmentSearchResultsInterface;

    public function delete(ShipmentInterface $shipment): bool;

    public function deleteById(int $shipmentId): bool;

    public function getShipmentsByOrder(OrderInterface $order): ShipmentSearchResultsInterface;
}
