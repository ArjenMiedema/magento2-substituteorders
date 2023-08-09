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

use Dealer4Dealer\SubstituteOrders\Api\Data\Dealer4Dealer;
use Dealer4Dealer\SubstituteOrders\Api\Data\ShipmentTrackingInterface;

class ShipmentTracking implements ShipmentTrackingInterface
{
    public function __construct(
        private string $carrierName = '',
        private string $code = '',
        private string $url = ''
    ) {
    }

    public static function createByArray(array $data): ShipmentTrackingInterface
    {
        return new ShipmentTracking(
            $data['name'] ?? '',
            $data['code'] ?? '',
            $data['url'] ?? ''
        );
    }

    public function getArray(): array
    {
        return [
            'name' => $this->carrierName,
            'code' => $this->code,
            'url' => $this->url,
        ];
    }

    public function getCarrierName(): string
    {
        return $this->carrierName;
    }

    public function setCarrierName(string $carrierName): self
    {
        $this->carrierName = $carrierName;

        return $this;
    }

    public function getTrackingCode(): string
    {
        return $this->code;
    }

    public function setTrackingCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getTrackingUrl(): string
    {
        return $this->url;
    }

    public function setTrackingUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }
}
