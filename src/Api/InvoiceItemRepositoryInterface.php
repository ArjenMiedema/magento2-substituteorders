<?php

namespace Dealer4Dealer\SubstituteOrders\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\InvoiceItemInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\InvoiceItemSearchResultsInterface;
use Magento\Framework\Api\SearchResults;

interface InvoiceItemRepositoryInterface
{
    /**
     * @param InvoiceItemInterface $invoiceItem
     *
     * @return InvoiceItemInterface
     */
    public function save(InvoiceItemInterface $invoiceItem): InvoiceItemInterface;

    /**
     * @param int $invoiceItemId
     *
     * @return InvoiceItemInterface
     */
    public function getById(int $invoiceItemId): InvoiceItemInterface;

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return SearchResults
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResults;

    /**
     * @param InvoiceItemInterface $invoiceItem
     *
     * @return bool
     */
    public function delete(InvoiceItemInterface $invoiceItem): bool;

    /**
     * @param int $invoiceItemId
     *
     * @return bool
     */
    public function deleteById(int $invoiceItemId): bool;
}
