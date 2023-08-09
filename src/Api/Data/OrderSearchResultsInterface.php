<?php

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface OrderSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return OrderInterface[]
     */
    public function getItems(): array;

    /**
     * @param OrderInterface[] $items
     */
    public function setItems(array $items): self;
}
