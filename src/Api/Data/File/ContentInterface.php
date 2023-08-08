<?php

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Api\Data\File;

interface ContentInterface
{
    public function getFileData(): string;

    public function setFileData(string $fileData): self;

    public function getName(): string;

    public function setName(string $name): self;
}
