<?php

namespace Dealer4Dealer\SubstituteOrders\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface OrderAddressSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return OrderAddressInterface[]
     */
    public function getItems(): array;

    /**
     * @param OrderAddressInterface[] $items
     */
    public function setItems(array $items): self;
}
