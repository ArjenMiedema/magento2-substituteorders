<?php

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Model\File;

use Dealer4Dealer\SubstituteOrders\Api\Data\File\ContentInterface;
use Magento\Framework\Exception\InputException;

class ContentValidator
{
    /**
     * @throws InputException
     */
    public function isValid(ContentInterface $fileContent): bool
    {
        $decodedContent = base64_decode($fileContent->getFileData(), true);

        if ($decodedContent === false) {
            throw new InputException(__('Provided content must be valid base64 encoded data.'));
        }

        if (!$this->isFileNameValid($fileContent->getName())) {
            throw new InputException(__('Provided file name contains forbidden characters.'));
        }

        return true;
    }

    protected function isFileNameValid(string $fileName): bool
    {
        return preg_match('/^[^\\/?*:";<>()|{}\\\\]+$/', $fileName);
    }
}
