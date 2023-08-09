<?php

namespace Dealer4Dealer\SubstituteOrders\Api;

use Dealer4Dealer\SubstituteOrders\Api\Data\OrderInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\ShipmentInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\ShipmentSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResults;

interface ShipmentRepositoryInterface
{
    /**
     * @param ShipmentInterface $shipment
     *
     * @return ShipmentInterface
     */
    public function save(ShipmentInterface $shipment): ShipmentInterface;

    /**
     * @param int $shipmentId
     *
     * @return ShipmentInterface
     */
    public function getById(int $shipmentId): ShipmentInterface;

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return SearchResults
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResults;

    /**
     * @param ShipmentInterface $shipment
     *
     * @return bool
     */
    public function delete(ShipmentInterface $shipment): bool;

    /**
     * @param int $shipmentId
     *
     * @return bool
     */
    public function deleteById(int $shipmentId): bool;

    /**
     * @param OrderInterface $order
     *
     * @return array
     */
    public function getShipmentsByOrder(OrderInterface $order): array;
}
