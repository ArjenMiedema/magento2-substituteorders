<?php

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Api\Data;

interface InvoiceInterface
{
    public const ORDER_IDS = 'order_ids',
        BASE_SUBTOTAL = 'base_subtotal',
        PO_NUMBER = 'po_number',
        BASE_GRANDTOTAL = 'base_grandtotal',
        SUBTOTAL = 'subtotal',
        INVOICE_ID = 'invoice_id',
        STATE = 'state',
        BILLING_ADDRESS_ID = 'billing_address_id',
        BASE_DISCOUNT_AMOUNT = 'base_discount_amount',
        MAGENTO_CUSTOMER_ID = 'magento_customer_id',
        EXT_INVOICE_ID = 'ext_invoice_id',
        SHIPPING_AMOUNT = 'shipping_amount',
        BASE_TAX_AMOUNT = 'base_tax_amount',
        BASE_SHIPPING_AMOUNT = 'base_shipping_amount',
        GRANDTOTAL = 'grandtotal',
        MAGENTO_INVOICE_ID = 'magento_invoice_id',
        SHIPPING_ADDRESS_ID = 'shipping_address_id',
        INVOICE_DATE = 'invoice_date',
        UPDATED_AT = 'updated_at',
        TAX_AMOUNT = 'tax_amount',
        DISCOUNT_AMOUNT = 'discount_amount',
        MAGENTO_INCREMENT_ID = 'magento_increment_id',
        ADDITIONAL_DATA = 'additional_data',
        FILE_CONTENT = 'file_content';

    public function getInvoiceId(): ?int;

    public function setInvoiceId(int $invoiceId): self;

    public function getOrderIds(): ?array;

    public function setOrderIds(array $orderIds): self;

    public function getMagentoInvoiceId(): ?int;

    public function setMagentoInvoiceId(int $magentoInvoiceId): self;

    public function getExtInvoiceId(): ?string;

    public function setExtInvoiceId(string $extInvoiceId): self;

    public function getPoNumber(): ?string;

    public function setPoNumber(string $poNumber): self;

    public function getMagentoCustomerId(): ?int;

    public function setMagentoCustomerId(int $magentoCustomerId): self;

    public function getBaseTaxAmount(): ?float;

    public function setBaseTaxAmount(float $baseTaxAmount): self;

    public function getBaseDiscountAmount(): ?float;

    public function setBaseDiscountAmount(float $baseDiscountAmount): self;

    public function getBaseShippingAmount(): ?float;

    public function setBaseShippingAmount(float $baseShippingAmount): self;

    public function getBaseSubtotal(): ?float;

    public function setBaseSubtotal(float $baseSubtotal): self;

    public function getBaseGrandTotal(): ?float;

    public function setBaseGrandTotal(float $baseGrandTotal): self;

    public function getTaxAmount(): ?float;

    public function setTaxAmount(float $taxAmount): self;

    public function getDiscountAmount(): ?float;

    public function setDiscountAmount(float $discountAmount): self;

    public function getShippingAmount(): ?float;

    public function setShippingAmount(float $shippingAmount): self;

    public function getSubtotal(): ?float;

    public function setSubtotal(float $subtotal): self;

    public function getGrandtotal(): ?float;

    public function setGrandtotal(float $grandTotal): self;

    public function getInvoiceDate(): ?string;

    public function setInvoiceDate(string $invoiceDate): self;

    public function getState(): ?string;

    public function setState(string $state): self;

    public function getMagentoIncrementId(): ?string;

    public function setMagentoIncrementId(string $magentoMagentoIncrementId): self;

    /**
     * @return AdditionalDataInterface[]
     */
    public function getAdditionalData(): array;

    /**
     * @param AdditionalDataInterface[] $additionalData
     */
    public function setAdditionalData(array $additionalData): self;

    /**
     * @param InvoiceItemInterface[] $items
     */
    public function setItems(array $items): self;

    /**
     * @return InvoiceItemInterface[] $items
     */
    public function getItems(): array;

    public function getShippingAddress(): ?OrderAddressInterface;

    public function setShippingAddress(OrderAddressInterface $shippingAddress): self;

    public function getBillingAddress(): ?OrderAddressInterface;

    public function setBillingAddress(OrderAddressInterface $billingAddress): self;

    /**
     * @return AttachmentInterface[]
     */
    public function getAttachments(): array;

    /**
     * @param AttachmentInterface[] $attachments
     */
    public function setAttachments(array $attachments): self;
}
