<?php

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Api\Data;

interface OrderInvoiceRelationInterface
{
    public const ORDER_ID = 'order_id',
        INVOICE_ID = 'invoice_id';

    public function getOrderId(): ?int;

    public function setOrderId(int $orderId): self;

    public function getInvoiceId(): ?int;

    public function setInvoiceId(int $invoiceId): self;
}
