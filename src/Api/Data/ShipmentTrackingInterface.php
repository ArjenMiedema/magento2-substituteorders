<?php

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Api\Data;

interface ShipmentTrackingInterface
{
    public const CARRIER_NAME = 'carrier_name',
        TRACKING_CODE = 'tracking_code',
        TRACKING_URL = 'tracking_url';

    public function getCarrierName(): ?string;

    public function setCarrierName(string $carrierName): self;

    public function getTrackingCode(): ?string;

    public function setTrackingCode(string $code): self;

    public function getTrackingUrl(): ?string;

    public function setTrackingUrl(string $url): self;
}
