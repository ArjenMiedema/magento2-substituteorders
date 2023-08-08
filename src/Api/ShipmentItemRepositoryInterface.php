<?php

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Api;

use Dealer4Dealer\SubstituteOrders\Api\Data\ShipmentItemInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\ShipmentItemSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface ShipmentItemRepositoryInterface
{
    public function save(ShipmentItemInterface $shipmentItem): ShipmentItemInterface;

    public function getById(string $shipmentItemId): ShipmentItemInterface;

    public function getList(SearchCriteriaInterface $searchCriteria): ShipmentItemSearchResultsInterface;

    public function delete(ShipmentItemInterface $shipmentItem): bool;

    public function deleteById(int $shipmentItemId): bool;
}
