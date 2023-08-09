<?php

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Api\Data;

interface OrderInterface
{
    public const ORDER_ID = 'order_id',
        INVOICE_IDS = 'invoice_ids',
        MAGENTO_ORDER_ID = 'magento_order_id',
        EXT_ORDER_ID = 'ext_order_id',
        PO_NUMBER = 'po_number',
        MAGENTO_CUSTOMER_ID = 'magento_customer_id',
        SHIPPING_ADDRESS_ID = 'shipping_address_id',
        BILLING_ADDRESS_ID = 'billing_address_id',
        BASE_TAX_AMOUNT = 'base_tax_amount',
        BASE_DISCOUNT_AMOUNT = 'base_discount_amount',
        BASE_SHIPPING_AMOUNT = 'base_shipping_amount',
        BASE_SUBTOTAL = 'base_subtotal',
        BASE_GRANDTOTAL = 'base_grandtotal',
        SHIPPING_METHOD = 'shipping_method',
        TAX_AMOUNT = 'tax_amount',
        DISCOUNT_AMOUNT = 'discount_amount',
        SHIPPING_AMOUNT = 'shipping_amount',
        SUBTOTAL = 'subtotal',
        GRANDTOTAL = 'grandtotal',
        ORDER_DATE = 'order_date',
        STATE = 'state',
        PAYMENT_METHOD = 'payment_method',
        ADDITIONAL_DATA = 'additional_data',
        MAGENTO_INCREMENT_ID = 'magento_increment_id',
        UPDATED_AT = 'updated_at',
        ITEMS = 'items',
        FILE_CONTENT = 'file_content',
        EXTERNAL_CUSTOMER_ID = 'external_customer_id';

    public function getOrderId(): int;

    public function setOrderId(int $orderId): self;

    public function getInvoiceIds(): ?array;

    public function setInvoiceIds(array $invoiceIds): self;

    public function getMagentoOrderId(): ?int;

    public function setMagentoOrderId(int $magentoOrderId): self;

    public function getMagentoCustomerId(): ?int;

    public function setMagentoCustomerId(int $magentoCustomerId): self;

    public function getExternalCustomerId(): ?int;

    public function setExternalCustomerId(int $externalCustomerId): self;

    public function getExtOrderId(): ?string;

    public function setExtOrderId(string $extOrderId): self;

    public function getBaseGrandtotal(): ?float;

    public function setBaseGrandtotal(float $baseGrandtotal): self;

    public function getBaseSubtotal(): ?float;

    public function setBaseSubtotal(float $baseSubtotal): self;

    public function getGrandtotal(): ?float;

    public function setGrandtotal(float $grandtotal): self;

    public function getSubtotal(): ?float;

    public function setSubtotal(float $subtotal): self;

    public function getPoNumber(): ?string;

    public function setPoNumber(string $poNumber): self;

    public function getState(): ?string;

    public function setState(string $state): self;

    public function getShippingMethod(): ?string;

    public function setShippingMethod(string $shippingMethod): self;

    public function getShippingAddress(): ?OrderAddressInterface;

    public function setShippingAddress(OrderAddressInterface $shippingAddress): self;

    public function getBillingAddress(): ?OrderAddressInterface;

    public function setBillingAddress(OrderAddressInterface $billingAddress): self;

    public function getPaymentMethod(): ?string;

    public function setPaymentMethod(string $paymentMethod): self;

    public function getBaseDiscountAmount(): ?float;

    public function setBaseDiscountAmount(float $baseDiscountAmount): self;

    public function getDiscountAmount(): ?float;

    public function setDiscountAmount(float $discountAmount): self;

    public function getOrderDate(): ?string;

    public function setOrderDate(string $orderDate): self;

    public function getBaseTaxAmount(): ?float;

    public function setBaseTaxAmount(float $baseTaxAmount): self;

    public function getTaxAmount(): ?float;

    public function setTaxAmount(float $taxAmount): self;

    public function getBaseShippingAmount(): ?float;

    public function setBaseShippingAmount(float $baseShippingAmount): self;

    public function getShippingAmount(): ?float;

    public function setShippingAmount(float $shippingAmount): self;

    public function setItems(array $items): self;

    public function getItems(): array;

    public function getMagentoIncrementId(): ?int;

    public function setMagentoIncrementId(int $incrementId): self;

    public function getUpdatedAt(): ?string;

    public function setUpdatedAt(string $updated): self;

    public function getAdditionalData(): array;

    public function setAdditionalData(array $additionalData): self;

    public function getAttachments(): ?array;

    public function setAttachments(array $fileContent): self;
}
