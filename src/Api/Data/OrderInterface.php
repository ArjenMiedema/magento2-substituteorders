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

    public function getOrderId(): string;

    public function setOrderId(string $orderId): self;

    public function getInvoiceIds(): ?array;

    public function setInvoiceIds(array $invoiceIds): self;

    public function getMagentoOrderId(): ?string;

    public function setMagentoOrderId(string $magentoOrderId): self;

    public function getMagentoCustomerId(): ?string;

    public function setMagentoCustomerId(string $magentoCustomerId): self;

    public function getExternalCustomerId(): ?string;

    public function setExternalCustomerId(string $externalCustomerId): self;

    public function getExtOrderId(): ?string;

    public function setExtOrderId(string $extOrderId): self;

    public function getBaseGrandtotal(): ?string;

    public function setBaseGrandtotal(string $baseGrandtotal): self;

    public function getBaseSubtotal(): ?string;

    public function setBaseSubtotal(string $baseSubtotal): self;

    public function getGrandtotal(): ?string;

    public function setGrandtotal(string $grandtotal): self;

    public function getSubtotal(): ?string;

    public function setSubtotal(string $subtotal): self;

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

    public function getBaseDiscountAmount(): ?string;

    public function setBaseDiscountAmount(string $baseDiscountAmount): self;

    public function getDiscountAmount(): ?string;

    public function setDiscountAmount(string $discountAmount): self;

    public function getOrderDate(): ?string;

    public function setOrderDate(string $orderDate): self;

    public function getBaseTaxAmount(): ?string;

    public function setBaseTaxAmount(string $baseTaxAmount): self;

    public function getTaxAmount(): ?string;

    public function setTaxAmount(string $taxAmount): self;

    public function getBaseShippingAmount(): ?string;

    public function setBaseShippingAmount(string $baseShippingAmount): self;

    public function getShippingAmount(): ?string;

    public function setShippingAmount(string $shippingAmount): self;

    public function setItems(array $items): self;

    public function getItems(): array;

    public function getMagentoIncrementId(): ?string;

    public function setMagentoIncrementId(string $incrementId): self;

    public function getUpdatedAt(): ?string;

    public function setUpdatedAt(string $updated): self;

    public function getAdditionalData(): array;

    public function setAdditionalData(array $additionalData): self;

    public function getAttachments(): ?array;

    public function setAttachments(array $fileContent): self;
}
