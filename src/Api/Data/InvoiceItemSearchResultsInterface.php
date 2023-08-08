<?php

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface InvoiceItemSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return InvoiceItemInterface[]
     */
    public function getItems(): array;

    /**
     * @param InvoiceItemInterface[] $items
     */
    public function setItems(array $items): self;
}
