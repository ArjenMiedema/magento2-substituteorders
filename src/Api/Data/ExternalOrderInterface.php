<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Api\Data;

interface ExternalOrderInterface
{
    public const ORDER_ID    = 'order_id',
        INVOICE_IDS          = 'invoice_ids',
        MAGENTO_ORDER_ID     = 'magento_order_id',
        EXT_ORDER_ID         = 'ext_order_id',
        PO_NUMBER            = 'po_number',
        MAGENTO_CUSTOMER_ID  = 'magento_customer_id',
        SHIPPING_ADDRESS_ID  = 'shipping_address_id',
        BILLING_ADDRESS_ID   = 'billing_address_id',
        BASE_TAX_AMOUNT      = 'base_tax_amount',
        BASE_DISCOUNT_AMOUNT = 'base_discount_amount',
        BASE_SHIPPING_AMOUNT = 'base_shipping_amount',
        BASE_SUBTOTAL        = 'base_subtotal',
        BASE_GRANDTOTAL      = 'base_grandtotal',
        SHIPPING_METHOD      = 'shipping_method',
        TAX_AMOUNT           = 'tax_amount',
        DISCOUNT_AMOUNT      = 'discount_amount',
        SHIPPING_AMOUNT      = 'shipping_amount',
        SUBTOTAL             = 'subtotal',
        GRANDTOTAL           = 'grandtotal',
        ORDER_DATE           = 'order_date',
        STATE                = 'state',
        PAYMENT_METHOD       = 'payment_method',
        ADDITIONAL_DATA      = 'additional_data',
        MAGENTO_INCREMENT_ID = 'magento_increment_id',
        UPDATED_AT           = 'updated_at',
        ITEMS                = 'items',
        FILE_CONTENT         = 'file_content',
        EXTERNAL_CUSTOMER_ID = 'external_customer_id';

    /**
     * @return int
     */
    public function getId(): int;

    /**
     * @return int[]
     */
    public function getInvoiceIds(): array;

    /**
     * @return int
     */
    public function getMagentoOrderId(): int;

    /**
     * @return int
     */
    public function getExternalOrderId(): int;

    /**
     * @return string
     */
    public function getPoNumber(): string;

    /**
     * @return int
     */
    public function getMagentoCustomerId(): int;

    /**
     * @return int
     */
    public function getShippingAddressId(): int;

    /**
     * @return int
     */
    public function getBillingAddressId(): int;

    /**
     * @return float
     */
    public function getBaseTaxAmount(): float;

    /**
     * @return float
     */
    public function getBaseDiscountAmount(): float;

    /**
     * @return float
     */
    public function getBaseShippingAmount(): float;

    /**
     * @return float
     */
    public function getBaseSubtotal(): float;

    /**
     * @return float
     */
    public function getBaseGrandTotal(): float;

    /**
     * @return string
     */
    public function getShippingMethod(): string;

    /**
     * @return float
     */
    public function getTaxAmount(): float;

    /**
     * @return float
     */
    public function getDiscountAmount(): float;

    /**
     * @return float
     */
    public function getShippingAmount(): float;

    /**
     * @return float
     */
    public function getSubtotal(): float;

    /**
     * @return float
     */
    public function getGrandTotal(): float;

    /**
     * @return string
     */
    public function getOrderDate(): string;

    /**
     * @return string
     */
    public function getState(): string;

    /**
     * @return string
     */
    public function getPaymentMethod(): string;

    /**
     * @return array
     */
    public function getAdditionalData(): array;

    /**
     * @return string
     */
    public function getMagentoIncrementId(): string;

    /**
     * @return string
     */
    public function getUpdatedAt(): string;

    /**
     * @return array
     */
    public function getItems(): array;

    /**
     * @return string
     */
    public function getFileContent(): string;

    /**
     * @return int
     */
    public function getExternalCustomerId(): int;
}
