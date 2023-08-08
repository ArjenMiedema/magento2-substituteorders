<?php

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Api\Data;

interface AdditionalDataInterface
{
    public function getKey(): ?string;

    public function setKey(string $key): self;

    public function getValue(): ?string;

    public function setValue(string $value): self;
}
