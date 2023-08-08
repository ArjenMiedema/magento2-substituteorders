<?php

namespace Dealer4Dealer\SubstituteOrders\Api;

use Dealer4Dealer\SubstituteOrders\Api\Data\ShipmentInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\ShipmentSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface ShipmentManagementInterface
{
    /**
     * @param int $id
     *
     * @return ShipmentInterface
     */
    public function getShipmentById(int $id): ShipmentInterface;

    /**
     * @param int $id
     *
     * @return ShipmentInterface
     */
    public function getShipmentByExt(int $id): ShipmentInterface;

    /**
     * @param int $id
     *
     * @return ShipmentInterface
     */
    public function getShipmentByMagentoIncrement(int $id): ShipmentInterface;

    /**
     * @param ShipmentInterface $shipment
     *
     * @return int
     */
    public function postShipment(ShipmentInterface $shipment): int;

    /**
     * @param ShipmentInterface $shipment
     *
     * @return int
     */
    public function putShipment(ShipmentInterface $shipment): int;

    /**
     * @param int $id
     *
     * @return bool
     */
    public function deleteShipmentById(int $id): bool;

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return ShipmentSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): ShipmentSearchResultsInterface;
}
