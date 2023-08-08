<?php

namespace Dealer4Dealer\SubstituteOrders\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\InvoiceItemInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\InvoiceItemSearchResultsInterface;

interface InvoiceItemRepositoryInterface
{
    public function save(InvoiceItemInterface $invoiceItem): InvoiceItemInterface;

    public function getById(int $invoiceItemId): InvoiceItemInterface;

    public function getList(SearchCriteriaInterface $searchCriteria): InvoiceItemSearchResultsInterface;

    public function delete(InvoiceItemInterface $invoiceItem): bool;

    public function deleteById(int $invoiceItemId): bool;
}
