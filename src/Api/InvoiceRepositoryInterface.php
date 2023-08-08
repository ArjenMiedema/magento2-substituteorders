<?php

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Api;

use Dealer4Dealer\SubstituteOrders\Api\Data\OrderInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\InvoiceInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\InvoiceSearchResultsInterface;

interface InvoiceRepositoryInterface
{
    public function save(InvoiceInterface $invoice): InvoiceInterface;

    public function getById(string $invoiceId): InvoiceInterface;

    public function getList(SearchCriteriaInterface $searchCriteria): InvoiceSearchResultsInterface;

    public function delete(InvoiceInterface $invoice): bool;

    public function deleteById(int $invoiceId): bool;

    public function getInvoicesByOrder(OrderInterface $order): InvoiceSearchResultsInterface;
}
