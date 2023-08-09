<?php

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Model\File;

use Dealer4Dealer\SubstituteOrders\Api\Data\File\ContentInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

class Content extends AbstractExtensibleModel implements ContentInterface
{
    const DATA = 'file_data';
    const NAME = 'name';

    public function getFileData(): string
    {
        return $this->getData(self::DATA);
    }

    public function getName(): string
    {
        return $this->getData(self::NAME);
    }

    public function setFileData(string $fileData): self
    {
        return $this->setData(self::DATA, $fileData);
    }

    public function setName(string $name): self
    {
        return $this->setData(self::NAME, $name);
    }
}
