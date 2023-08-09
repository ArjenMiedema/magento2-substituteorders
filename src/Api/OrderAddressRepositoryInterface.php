<?php

namespace Dealer4Dealer\SubstituteOrders\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\OrderAddressInterface;
use Magento\Framework\Api\SearchResults;
use Magento\Sales\Api\Data\OrderAddressInterface as MagentoOrderAddressInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\OrderAddressSearchResultsInterface;

interface OrderAddressRepositoryInterface
{
    /**
     * @param OrderAddressInterface $orderAddress
     *
     * @return OrderAddressInterface
     */
    public function save(OrderAddressInterface $orderAddress): OrderAddressInterface;

    /**
     * @param int $orderAddressId
     *
     * @return OrderAddressInterface
     */
    public function getById(int $orderAddressId): OrderAddressInterface;

    /**
     * @param MagentoOrderAddressInterface $address
     *
     * @return OrderAddressInterface
     */
    public function saveByAddress(MagentoOrderAddressInterface $address): OrderAddressInterface;

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return SearchResults
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResults;

    /**
     * @param OrderAddressInterface $orderAddress
     *
     * @return bool
     */
    public function delete(OrderAddressInterface $orderAddress): bool;

    /**
     * @param int $orderAddressId
     *
     * @return bool
     */
    public function deleteById(int $orderAddressId): bool;
}
