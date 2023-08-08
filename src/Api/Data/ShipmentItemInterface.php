<?php

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Api\Data;

interface ShipmentItemInterface
{
    public const WEIGHT = 'weight',
        QTY = 'qty',
        PRICE = 'price',
        ROW_TOTAL = 'row_total',
        SHIPMENT_ID = 'shipment_id',
        NAME = 'name',
        DESCRIPTION = 'description',
        ADDITIONAL_DATA = 'additional_data',
        SHIPMENTITEM_ID = 'shipmentitem_id',
        SKU = 'sku';

    public function getShipmentItemId(): ?int;

    public function setShipmentItemId(int $shipmentItemId): self;

    public function getShipmentId(): ?int;

    public function setShipmentId(int $shipmentId): self;

    public function getRowTotal(): ?string;

    public function setRowTotal(string $rowTotal): self;

    public function getPrice(): ?string;

    public function setPrice(string $price): self;

    public function getWeight(): ?string;

    public function setWeight(string $weight): self;

    public function getQty(): ?string;

    public function setQty(string $qty): self;

    public function getSku(): ?string;

    public function setSku(string $sku): self;

    public function getName(): ?string;

    public function setName(string $name): self;

    public function getDescription(): ?string;

    public function setDescription(string $description): self;

    /**
     * @return AdditionalDataInterface[]
     */
    public function getAdditionalData(): array;

    /**
     * @param AdditionalDataInterface[] $additionalData
     */
    public function setAdditionalData(array $additionalData): self;
}
