<?php

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Api;

use Dealer4Dealer\SubstituteOrders\Api\Data\OrderInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\InvoiceInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\InvoiceSearchResultsInterface;
use Magento\Framework\Api\SearchResults;

interface InvoiceRepositoryInterface
{
    /**
     * @param InvoiceInterface $invoice
     *
     * @return InvoiceInterface
     */
    public function save(InvoiceInterface $invoice): InvoiceInterface;

    /**
     * @param int $invoiceId
     *
     * @return InvoiceInterface
     */
    public function getById(int $invoiceId): InvoiceInterface;

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return SearchResults
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResults;

    /**
     * @param InvoiceInterface $invoice
     *
     * @return bool
     */
    public function delete(InvoiceInterface $invoice): bool;

    /**
     * @param int $invoiceId
     *
     * @return bool
     */
    public function deleteById(int $invoiceId): bool;

    /**
     * @param OrderInterface $order
     *
     * @return SearchResults
     */
    public function getInvoicesByOrder(OrderInterface $order): SearchResults;
}
