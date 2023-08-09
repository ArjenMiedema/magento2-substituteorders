<?php

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\InvoiceInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\InvoiceSearchResultsInterface;
use Magento\Framework\Api\SearchResults;

interface InvoiceManagementInterface
{
    /**
     * @param int $id
     *
     * @return InvoiceInterface
     */
    public function getInvoice(int $id): InvoiceInterface;

    /**
     * @param int $id
     *
     * @return InvoiceInterface
     */
    public function getInvoiceByExt(int $id): InvoiceInterface;

    /**
     * @param int $id
     *
     * @return InvoiceInterface
     */
    public function getInvoiceByMagento(int $id): InvoiceInterface;

    /**
     * @param int $id
     *
     * @return InvoiceInterface
     */
    public function getInvoiceByMagentoIncrementId(int $id): InvoiceInterface;

    /**
     * @param InvoiceInterface $invoice
     *
     * @return int
     */
    public function postInvoice(InvoiceInterface $invoice): int;

    /**
     * @param InvoiceInterface $invoice
     *
     * @return int|null
     */
    public function putInvoice(InvoiceInterface $invoice): ?int;

    /**
     * @param int $id
     *
     * @return bool
     */
    public function deleteInvoice(int $id): bool;

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return SearchResults
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResults;

    /**
     * @param int $id
     *
     * @return SearchResults
     */
    public function getInvoicesByOrderIncrementId(int $id): SearchResults;
}
