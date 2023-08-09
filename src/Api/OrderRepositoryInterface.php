<?php

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Api;

use Dealer4Dealer\SubstituteOrders\Api\Data\OrderInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\OrderSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface OrderRepositoryInterface
{
    public function save(OrderInterface $order): OrderInterface;

    public function getById(int $orderId): OrderInterface;

    public function getByMagentoOrderId(int $id): OrderInterface;

    public function getByExtOrderId(int $id): OrderInterface;

    public function getList(SearchCriteriaInterface $searchCriteria): OrderSearchResultsInterface;

    public function delete(OrderInterface $order): bool;

    public function deleteById(int $orderId): bool;
}
