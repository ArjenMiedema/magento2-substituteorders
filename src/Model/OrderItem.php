<?php

/**
 * A Magento 2 module named Dealer4Dealer\SubstituteOrders
 * Copyright (C) 2017 Maikel Martens
 *
 * This file is part of Dealer4Dealer\SubstituteOrders.
 *
 * Dealer4Dealer\SubstituteOrders is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Dealer4Dealer\SubstituteOrders\Model;

use Dealer4Dealer\SubstituteOrders\Api\Data\OrderInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\OrderItemInterface;
use Dealer4Dealer\SubstituteOrders\Model\ResourceModel\OrderItem as ResourceModel;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

class OrderItem extends AbstractModel implements OrderItemInterface
{
    public const ENTITY = 'order_item';

    /**
     * @var string
     */
    protected $_eventPrefix = 'substitute_order_order_item';

    /**
     * @var string
     */
    protected $_eventObject = 'item';

    protected ?OrderInterface $order = null;

    protected ?array $additionalData = null;

    protected function _construct(): void
    {
        $this->_init(ResourceModel::class);
    }

    public function save(): self
    {
        if ($this->additionalData) {
            $data = [];
            foreach ($this->additionalData as $value) {
                $data[$value->getKey()] = $value->getValue();
            }

            $this->setData(self::ADDITIONAL_DATA, json_encode($data));
        }

        return parent::save();
    }

    public function getOrderitemId(): int
    {
        return $this->getData(self::ORDERITEM_ID);
    }

    public function setOrderitemId(int $orderItemId): self
    {
        return $this->setData(self::ORDERITEM_ID, $orderItemId);
    }

    public function setOrderId(int $orderId): self
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    public function getOrderId(): int
    {
        return $this->getData(self::ORDER_ID);
    }

    public function setInvoiceId(int $invoiceId): self
    {
        return $this->setData(self::INVOICE_ID, $invoiceId);
    }

    public function getInvoiceId(): int
    {
        return $this->getData(self::INVOICE_ID);
    }

    public function getName(): string
    {
        return $this->getData(self::NAME);
    }

    public function setName(string $name): self
    {
        return $this->setData(self::NAME, $name);
    }

    public function getSku(): string
    {
        return $this->getData(self::SKU);
    }

    public function setSku(string $sku): self
    {
        return $this->setData(self::SKU, $sku);
    }

    public function getBasePrice(): float
    {
        return $this->getData(self::BASE_PRICE);
    }

    public function setBasePrice(float $basePrice): self
    {
        return $this->setData(self::BASE_PRICE, $basePrice);
    }

    public function getPrice(): float
    {
        return $this->getData(self::PRICE);
    }

    public function setPrice(float $price): self
    {
        return $this->setData(self::PRICE, $price);
    }

    public function getBaseRowTotal(): float
    {
        return $this->getData(self::BASE_ROW_TOTAL);
    }

    public function setBaseRowTotal($baseRowTotal): self
    {
        return $this->setData(self::BASE_ROW_TOTAL, $baseRowTotal);
    }

    public function getRowTotal(): float
    {
        return $this->getData(self::ROW_TOTAL);
    }

    public function setRowTotal(float $rowTotal): self
    {
        return $this->setData(self::ROW_TOTAL, $rowTotal);
    }

    public function getBaseTaxAmount(): float
    {
        return $this->getData(self::BASE_TAX_AMOUNT);
    }

    public function setBaseTaxAmount(float $baseTaxAmount): self
    {
        return $this->setData(self::BASE_TAX_AMOUNT, $baseTaxAmount);
    }

    public function getTaxAmount(): float
    {
        return $this->getData(self::TAX_AMOUNT);
    }

    public function setTaxAmount(float $taxAmount): self
    {
        return $this->setData(self::TAX_AMOUNT, $taxAmount);
    }

    public function getQty(): float
    {
        return $this->getData(self::QTY);
    }

    public function setQty(float $qty): self
    {
        return $this->setData(self::QTY, $qty);
    }

    public function getAdditionalData(): array
    {
        if ($this->additionalData == null) {
            $this->additionalData = [];

            if ($this->getData(self::ADDITIONAL_DATA)) {
                $data = json_decode($this->getData(self::ADDITIONAL_DATA), true);
                foreach ($data as $key => $value) {
                    $this->additionalData[] = new AdditionalData($key, $value);
                }
            }
        }

        return $this->additionalData;
    }

    public function setAdditionalData(array $additionalData): self
    {
        $this->additionalData = $additionalData;
        return $this;
    }

    public function getBaseDiscountAmount(): float
    {
        return $this->getData(self::BASE_DISCOUNT_AMOUNT);
    }

    public function setBaseDiscountAmount(float $baseDiscountAmount): self
    {
        return $this->setData(self::BASE_DISCOUNT_AMOUNT, $baseDiscountAmount);
    }

    public function getDiscountAmount(): float
    {
        return $this->getData(self::DISCOUNT_AMOUNT);
    }

    public function setDiscountAmount(float $discountAmount): self
    {
        return $this->setData(self::DISCOUNT_AMOUNT, $discountAmount);
    }
}
