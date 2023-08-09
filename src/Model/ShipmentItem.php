<?php

declare(strict_types=1);

// phpcs:disable GlobalCommon.NamingConventions.ValidVariableName.IllegalVariableNameUnderscore
// phpcs:disable PSR2.Classes.PropertyDeclaration.Underscore
// phpcs:disable PSR2.Methods.MethodDeclaration.Underscore

namespace Dealer4Dealer\SubstituteOrders\Model;

use Dealer4Dealer\SubstituteOrders\Api\Data\ShipmentItemInterface;
use Dealer4Dealer\SubstituteOrders\Model\ResourceModel\ShipmentItem as ResourceShipmentItem;
use Dealer4Dealer\SubstituteOrders\Model\AdditionalData;
use Magento\Framework\Model\AbstractModel;

class ShipmentItem extends AbstractModel implements ShipmentItemInterface
{
    /**
     * @var string
     */
    public const ENTITY = 'shipment_item';

    /**
     * @var string
     */
    protected $_eventPrefix = 'substitute_order_shipment_item';

    /**
     * @var string
     */
    protected $_eventObject = 'item';

    protected ?array $additionalData;

    protected function _construct(): void
    {
        $this->_init(ResourceShipmentItem::class);
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

    public function getShipmentItemId(): int
    {
        return $this->getData(self::SHIPMENTITEM_ID);
    }

    public function setShipmentItemId(int $shipmentItemId): self
    {
        return $this->setData(self::SHIPMENTITEM_ID, $shipmentItemId);
    }

    public function getShipmentId(): int
    {
        return $this->getData(self::SHIPMENT_ID);
    }

    public function setShipmentId(int $shipmentId): self
    {
        return $this->setData(self::SHIPMENT_ID, $shipmentId);
    }

    public function getRowTotal(): float
    {
        return $this->getData(self::ROW_TOTAL);
    }

    public function setRowTotal(float $rowTotal): self
    {
        return $this->setData(self::ROW_TOTAL, $rowTotal);
    }

    public function getPrice(): float
    {
        return $this->getData(self::PRICE);
    }

    public function setPrice(float $price): self
    {
        return $this->setData(self::PRICE, $price);
    }

    public function getWeight(): float
    {
        return $this->getData(self::WEIGHT);
    }

    public function setWeight(float $weight): self
    {
        return $this->setData(self::WEIGHT, $weight);
    }

    public function getQty(): float
    {
        return $this->getData(self::QTY);
    }

    public function setQty(float $qty): self
    {
        return $this->setData(self::QTY, $qty);
    }

    public function getSku(): string
    {
        return $this->getData(self::SKU);
    }

    public function setSku(string $sku): self
    {
        return $this->setData(self::SKU, $sku);
    }

    public function getName(): string
    {
        return $this->getData(self::NAME);
    }

    public function setName(string $name): self
    {
        return $this->setData(self::NAME, $name);
    }

    public function getDescription(): string
    {
        return $this->getData(self::DESCRIPTION);
    }

    public function setDescription(string $description): self
    {
        return $this->setData(self::DESCRIPTION, $description);
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
}
