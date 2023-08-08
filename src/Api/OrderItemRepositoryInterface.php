<?php

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\OrderItemInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\OrderItemSearchResultsInterface;

interface OrderItemRepositoryInterface
{
    public function save(OrderItemInterface $orderItem): OrderItemInterface;

    public function getById(int $orderItemId): OrderItemInterface;

    public function getList(SearchCriteriaInterface $searchCriteria): OrderItemSearchResultsInterface;

    public function delete(OrderItemInterface $orderItem): bool;

    public function deleteById(int $orderItemId): bool;
}
