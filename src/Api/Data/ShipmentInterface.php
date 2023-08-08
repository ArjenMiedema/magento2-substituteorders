<?php

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Api\Data;

interface ShipmentInterface
{
    public const EXT_SHIPMENT_ID = 'ext_shipment_id',
        INVOICE_ID = 'invoice_id',
        SHIPMENT_STATUS = 'shipment_status',
        BILLING_ADDRESS_ID = 'billing_address_id',
        SHIPMENT_ID = 'shipment_id',
        ORDER_ID = 'order_id',
        NAME = 'name',
        CUSTOMER_ID = 'customer_id',
        UPDATED_AT = 'updated_at',
        SHIPPING_ADDRESS_ID = 'shipping_address_id',
        INCREMENT_ID = 'increment_id',
        CREATED_AT = 'created_at',
        TRACKING = 'tracking',
        ADDITIONAL_DATA = 'additional_data',
        FILE_CONTENT = 'file_content',
        SHIPPING_METHOD = 'shipping_method';

    public function getShipmentId(): ?int;

    public function setShipmentId(int $shipmentId): self;

    public function getExtShipmentId(): ?string;

    public function setExtShipmentId(string $extShipmentId): self;

    public function getCustomerId(): ?int;

    public function setCustomerId(int $customerId): self;

    public function getOrderId(): ?int;

    public function setOrderId(int $orderId): self;

    public function getInvoiceId(): ?int;

    public function setInvoiceId(int $invoiceId): self;

    public function getShipmentStatus(): ?string;

    public function setShipmentStatus(string $shipmentStatus): self;

    public function getIncrementId(): ?string;

    public function setIncrementId(string $incrementId): self;

    public function getCreatedAt(): ?string;

    public function setCreatedAt(string $createdAt): self;

    public function getUpdatedAt(): ?string;

    public function setUpdatedAt(string $updatedAt): self;

    public function getName(): ?string;

    public function setName(string $name): self;

    public function getTracking(): array;

    public function setTracking(array $tracking): self;

    /**
     * @return AdditionalDataInterface[]
     */
    public function getAdditionalData(): array;

    /**
     * @param AdditionalDataInterface[] $additionalData
     */
    public function setAdditionalData(array $additionalData): self;

    /**
     * @param ShipmentItemInterface[] $items
     */
    public function setItems(array $items): self;

    /**
     * @return ShipmentItemInterface[]
     */
    public function getItems(): array;

    public function getShippingAddress(): ?OrderAddressInterface;

    public function setShippingAddress(OrderAddressInterface $shippingAddress): self;

    public function getBillingAddress(): ?OrderAddressInterface;

    public function setBillingAddress(OrderAddressInterface $billingAddress): self;

    /**
     * @return AttachmentInterface[]|null
     */
    public function getAttachments(): ?array;

    /**
     * @param AttachmentInterface[] $attachments
     */
    public function setAttachments(array $attachments): self;

    public function getShippingMethod(): ?string;

    public function setShippingMethod(string $shippingMethod): self;
}
