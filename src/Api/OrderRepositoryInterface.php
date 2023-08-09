<?php

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Api;

use Dealer4Dealer\SubstituteOrders\Api\Data\OrderInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\OrderSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResults;

interface OrderRepositoryInterface
{
    /**
     * @param OrderInterface $order
     *
     * @return OrderInterface
     */
    public function save(OrderInterface $order): OrderInterface;

    /**
     * @param int $orderId
     *
     * @return OrderInterface
     */
    public function getById(int $orderId): OrderInterface;

    /**
     * @param int $id
     *
     * @return OrderInterface
     */
    public function getByMagentoOrderId(int $id): OrderInterface;

    /**
     * @param int $id
     *
     * @return OrderInterface
     */
    public function getByExtOrderId(int $id): OrderInterface;

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return SearchResults
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResults;

    /**
     * @param OrderInterface $order
     *
     * @return bool
     */
    public function delete(OrderInterface $order): bool;

    /**
     * @param int $orderId
     *
     * @return bool
     */
    public function deleteById(int $orderId): bool;
}
