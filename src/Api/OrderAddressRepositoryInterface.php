<?php

namespace Dealer4Dealer\SubstituteOrders\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\Data\OrderAddressInterface as MagentoOrderAddressInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\OrderAddressSearchResultsInterface;

interface OrderAddressRepositoryInterface
{
    public function save(OrderAddressInterface $orderAddress): OrderAddressInterface;

    public function getById(int $orderAddressId): OrderAddressInterface;

    public function saveByAddress(MagentoOrderAddressInterface $address): OrderAddressInterface;

    public function getList(SearchCriteriaInterface $searchCriteria): OrderAddressSearchResultsInterface;

    public function delete(OrderAddressInterface $orderAddress): bool;

    public function deleteById(int $orderAddressId): bool;
}
