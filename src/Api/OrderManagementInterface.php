<?php

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Api;

use Dealer4Dealer\SubstituteOrders\Api\Data\ExternalOrderInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;

interface OrderManagementInterface
{
    /**
     * @param ExternalOrderInterface $externalOrder
     *
     * @return int
     */
    public function update(ExternalOrderInterface $externalOrder): int;

    /**
     * @param string $id
     *
     * @return ExternalOrderInterface
     */
    public function get(string $id): ExternalOrderInterface;

    /**
     * @param int $id
     *
     * @return ExternalOrderInterface
     */
    public function getById(int $id): ExternalOrderInterface;

    /**
     * @param ExternalOrderInterface $externalOrder
     *
     * @return int
     */
    public function create(ExternalOrderInterface $externalOrder): int;

    /**
     * @param int $id
     *
     * @return void
     */
    public function deleteOrderById(int $id): void;

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return OrderSearchResultInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): OrderSearchResultInterface;
}
