<?php

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\OrderItemInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\OrderItemSearchResultsInterface;
use Magento\Framework\Api\SearchResults;

interface OrderItemRepositoryInterface
{
    /**
     * @param OrderItemInterface $orderItem
     *
     * @return OrderItemInterface
     */
    public function save(OrderItemInterface $orderItem): OrderItemInterface;

    /**
     * @param int $orderItemId
     *
     * @return OrderItemInterface
     */
    public function getById(int $orderItemId): OrderItemInterface;

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return SearchResults
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResults;

    /**
     * @param OrderItemInterface $orderItem
     *
     * @return bool
     */
    public function delete(OrderItemInterface $orderItem): bool;

    /**
     * @param int $orderItemId
     *
     * @return bool
     */
    public function deleteById(int $orderItemId): bool;
}
