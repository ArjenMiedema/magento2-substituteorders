<?php

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Api;

use Dealer4Dealer\SubstituteOrders\Api\Data\ShipmentItemInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\ShipmentItemSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResults;

interface ShipmentItemRepositoryInterface
{
    /**
     * @param ShipmentItemInterface $shipmentItem
     *
     * @return ShipmentItemInterface
     */
    public function save(ShipmentItemInterface $shipmentItem): ShipmentItemInterface;

    /**
     * @param int $shipmentItemId
     *
     * @return ShipmentItemInterface
     */
    public function getById(int $shipmentItemId): ShipmentItemInterface;

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return SearchResults
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResults;

    /**
     * @param ShipmentItemInterface $shipmentItem
     *
     * @return bool
     */
    public function delete(ShipmentItemInterface $shipmentItem): bool;

    /**
     * @param int $shipmentItemId
     *
     * @return bool
     */
    public function deleteById(int $shipmentItemId): bool;
}
