<?php

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Api\Data\File;

use InvalidArgumentException;

interface ContentUploaderInterface
{
    /**
     * @throws InvalidArgumentException
     */
    public function upload(
        ContentInterface $fileContent,
        string $customerIdentifier,
        string $entityType
    );
}
