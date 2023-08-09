<?php

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Model;

use Dealer4Dealer\SubstituteOrders\Api\Data\InvoiceItemInterface;
use Dealer4Dealer\SubstituteOrders\Model\AdditionalData;
use Dealer4Dealer\SubstituteOrders\Model\ResourceModel\InvoiceItem as ResourceModel;
use Magento\Framework\Model\AbstractModel;

class InvoiceItem extends AbstractModel implements InvoiceItemInterface
{
    public const ENTITY = 'invoice_item';

    /**
     * @var string
     */
    protected $_eventPrefix = 'substitute_order_invoice_item';

    /**
     * @var string
     */
    protected $_eventObject = 'item';

    protected ?array $additionalData;

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }

    public function save(): self
    {
        if ($this->additionalData !== null) {
            $data = [];

            foreach ($this->additionalData as $value) {
                $data[$value->getKey()] = $value->getValue();
            }

            $this->setData(self::ADDITIONAL_DATA, json_encode($data));
        }

        return parent::save();
    }

    public function getInvoiceitemId(): int
    {
        return $this->getData(self::INVOICEITEM_ID);
    }

    public function setInvoiceitemId(int $invoiceItemId): self
    {
        return $this->setData(self::INVOICEITEM_ID, $invoiceItemId);
    }

    public function setInvoiceId(int $invoiceId): self
    {
        return $this->setData(self::INVOICE_ID, $invoiceId);
    }

    public function getInvoiceId(): int
    {
        return $this->getData(self::INVOICE_ID);
    }

    public function setOrderId(int $orderId): self
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    public function getOrderId(): int
    {
        return $this->getData(self::ORDER_ID);
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

    public function setBaseRowTotal(float $baseRowTotal): self
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
        if ($this->additionalData === null) {
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
