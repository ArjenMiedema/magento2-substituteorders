<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Model;

use Dealer4Dealer\SubstituteOrders\Api\Data\ExternalOrderInterface;
use Magento\Framework\DataObject;

class ExternalOrder extends DataObject implements ExternalOrderInterface
{
    public function getId(): int
    {
        return $this->getData(static::ORDER_ID);
    }

    public function getInvoiceIds(): array
    {
        return $this->getData(static::INVOICE_IDS);
    }

    public function getMagentoOrderId(): int
    {
        return $this->getData(static::MAGENTO_ORDER_ID);
    }

    public function getExternalOrderId(): int
    {
        return $this->getData(static::EXT_ORDER_ID);
    }

    public function getPoNumber(): string
    {
        return $this->getData(static::PO_NUMBER);
    }

    public function getMagentoCustomerId(): int
    {
        return $this->getData(static::MAGENTO_CUSTOMER_ID);
    }

    public function getShippingAddressId(): int
    {
        return $this->getData(static::SHIPPING_ADDRESS_ID);
    }

    public function getBillingAddressId(): int
    {
        return $this->getData(static::BILLING_ADDRESS_ID);
    }

    public function getBaseTaxAmount(): float
    {
        return $this->getData(static::BASE_TAX_AMOUNT);
    }

    public function getBaseDiscountAmount(): float
    {
        return $this->getData(static::BASE_DISCOUNT_AMOUNT);
    }

    public function getBaseShippingAmount(): float
    {
        return $this->getData(static::BASE_SHIPPING_AMOUNT);
    }

    public function getBaseSubtotal(): float
    {
        return $this->getData(static::BASE_SUBTOTAL);
    }

    public function getBaseGrandTotal(): float
    {
        return $this->getData(static::BASE_GRANDTOTAL);
    }

    public function getShippingMethod(): string
    {
        return $this->getData(static::SHIPPING_METHOD);
    }

    public function getTaxAmount(): float
    {
        return $this->getData(static::TAX_AMOUNT);
    }

    public function getDiscountAmount(): float
    {
        return $this->getData(static::DISCOUNT_AMOUNT);
    }

    public function getShippingAmount(): float
    {
        return $this->getData(static::SHIPPING_AMOUNT);
    }

    public function getSubtotal(): float
    {
        return $this->getData(static::SUBTOTAL);
    }

    public function getGrandTotal(): float
    {
        return $this->getData(static::GRANDTOTAL);
    }

    public function getOrderDate(): string
    {
        return $this->getData(static::ORDER_DATE);
    }

    public function getState(): string
    {
        return $this->getData(static::STATE);
    }

    public function getPaymentMethod(): string
    {
        return $this->getData(static::PAYMENT_METHOD);
    }

    public function getAdditionalData(): array
    {
        return $this->getData(static::ADDITIONAL_DATA);
    }

    public function getMagentoIncrementId(): string
    {
        return $this->getData(static::MAGENTO_INCREMENT_ID);
    }

    public function getUpdatedAt(): string
    {
        return $this->getData(static::UPDATED_AT);
    }

    public function getItems(): array
    {
        return $this->getData(static::ITEMS);
    }

    public function getFileContent(): string
    {
        return $this->getData(static::FILE_CONTENT);
    }

    public function getExternalCustomerId(): int
    {
        return $this->getData(static::EXTERNAL_CUSTOMER_ID);
    }
}
