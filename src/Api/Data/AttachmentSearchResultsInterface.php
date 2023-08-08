<?php

namespace Dealer4Dealer\SubstituteOrders\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface AttachmentSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return AttachmentInterface[]
     */
    public function getItems(): array;

    /**
     * @param AttachmentInterface[] $items
     */
    public function setItems(array $items): self;
}
