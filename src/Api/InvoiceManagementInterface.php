<?php

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\InvoiceInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\InvoiceSearchResultsInterface;

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
     * @return int
     */
    public function putInvoice(InvoiceInterface $invoice): int;

    /**
     * @param InvoiceInterface $invoice
     *
     * @return bool
     */
    public function deleteInvoice(InvoiceInterface $invoice): bool;

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return InvoiceSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): InvoiceSearchResultsInterface;

    /**
     * @param int $id
     *
     * @return InvoiceSearchResultsInterface
     */
    public function getInvoicesByOrderIncrementId(int $id): InvoiceSearchResultsInterface;
}
