<?php

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Api\Data;

interface InvoiceItemInterface
{
    public const INVOICEITEM_ID = 'invoiceitem_id',
        INVOICE_ID = 'invoice_id',
        ORDER_ID = 'order_id',
        SKU = 'sku',
        NAME = 'name',
        PRICE = 'price',
        QTY = 'qty',
        TAX_AMOUNT = 'tax_amount',
        DISCOUNT_AMOUNT = 'discount_amount',
        ROW_TOTAL = 'row_total',
        BASE_PRICE = 'base_price',
        BASE_TAX_AMOUNT = 'base_tax_amount',
        BASE_DISCOUNT_AMOUNT = 'base_discount_amount',
        BASE_ROW_TOTAL = 'base_row_total',
        ADDITIONAL_DATA = 'additional_data';

    public function getInvoiceitemId(): ?int;

    public function setInvoiceitemId(int $invoiceItemId): self;

    public function getInvoiceId(): ?int;

    public function setInvoiceId(int $invoiceId): self;

    public function getOrderId(): ?int;

    public function setOrderId(int $orderId): self;

    public function getName(): ?string;

    public function setName(string $name): self;

    public function getSku(): ?string;

    public function setSku(string $sku): self;

    public function getBasePrice(): ?float;

    public function setBasePrice(float $basePrice): self;

    public function getPrice(): ?float;

    public function setPrice(float $price): self;

    public function getBaseRowTotal(): ?float;

    public function setBaseRowTotal(float $baseRowTotal): self;

    public function getRowTotal(): ?float;

    public function setRowTotal(float $rowTotal): self;

    public function getBaseTaxAmount(): ?float;

    public function setBaseTaxAmount(float $baseTaxAmount): self;

    public function getTaxAmount(): ?float;

    public function setTaxAmount(float $taxAmount): self;

    public function getQty(): ?float;

    public function setQty(float $qty): self;

    /**
     * @return AdditionalDataInterface[]
     */
    public function getAdditionalData(): array;

    /**
     * @param AdditionalDataInterface[] $additionalData
     */
    public function setAdditionalData(array $additionalData): self;

    public function getBaseDiscountAmount(): ?float;

    public function setBaseDiscountAmount(float $baseDiscountAmount): self;

    public function getDiscountAmount(): ?float;

    public function setDiscountAmount(float $discountAmount): self;
}
