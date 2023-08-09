<?php

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Api;

use Dealer4Dealer\SubstituteOrders\Api\Data\OrderInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\OrderSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResults;

interface OrderManagementInterface
{
    /**
     * @param OrderInterface $order
     *
     * @return int
     */
    public function postOrder(OrderInterface $order): int;

    /**
     * @param int $id
     *
     * @return OrderInterface
     */
    public function getOrderById(int $id): OrderInterface;

    /**
     * @param int $id
     *
     * @return OrderInterface
     */
    public function getOrderByMagento(int $id): OrderInterface;

    /**
     * @param int $id
     *
     * @return OrderInterface
     */
    public function getOrderByExt(int $id): OrderInterface;

    /**
     * @param int $id
     *
     * @return OrderInterface
     */
    public function getOrderByMagentoIncrementId(int $id): OrderInterface;

    /**
     * @param OrderInterface $order
     *
     * @return int
     */
    public function putOrder(OrderInterface $order): int;

    /**
     * @param int $id
     *
     * @return bool
     */
    public function deleteOrderById(int $id): bool;

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return SearchResults
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResults;
}
