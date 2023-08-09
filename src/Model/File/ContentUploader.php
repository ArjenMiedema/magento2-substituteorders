<?php

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Model\File;

use Dealer4Dealer\SubstituteOrders\Api\Data\File\ContentUploaderInterface;
use Dealer4Dealer\SubstituteOrders\Model\Attachment as AttachmentConfig;
use InvalidArgumentException;
use Laminas\Mime\Mime;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\MediaStorage\Helper\File\Storage;
use Magento\MediaStorage\Helper\File\Storage\Database;
use Magento\MediaStorage\Model\File\Uploader;
use Dealer4Dealer\SubstituteOrders\Api\Data\File\ContentInterface;
use Magento\MediaStorage\Model\File\Validator\NotProtectedExtension;
use Magento\Framework\Filesystem;

class ContentUploader extends Uploader implements ContentUploaderInterface
{
    private readonly Filesystem\Directory\WriteInterface $tmpDirectory;

    private readonly Filesystem\Directory\WriteInterface $mediaDirectory;

    public function __construct(
        private readonly AttachmentConfig $attachmentConfig,
        Filesystem $filesystem
    ) {
        $this->tmpDirectory   = $filesystem->getDirectoryWrite(DirectoryList::SYS_TMP);
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
    }

    /**
     * Decode base64 encoded content and save it in the system tmp folder
     */
    protected function decodeContent(ContentInterface $fileContent): array
    {
        $tmpFileName = $this->getTmpFileName();
        $fileSize    = $this->tmpDirectory->writeFile(
            $tmpFileName,
            base64_decode($fileContent->getFileData())
        );

        return [
            'name' => $fileContent->getName(),
            'type' => Mime::TYPE_OCTETSTREAM,
            'tmp_name' => $this->tmpDirectory->getAbsolutePath($tmpFileName),
            'error' => 0,
            'size' => $fileSize,
        ];
    }

    protected function getTmpFileName(): string
    {
        return sprintf(
            '%s.%s',
            uniqid(),
            $this->getFileExtension()
        );
    }

    public function upload(
        ContentInterface $fileContent,
        int $customerIdentifier,
        string $entityType = 'order'
    ): array {
        $this->_file = $this->decodeContent($fileContent);

        if (!file_exists($this->_file['tmp_name'])) {
            throw new InvalidArgumentException('There was an error during file content upload.');
        }

        $this->_fileExists = true;
        $this->_uploadType = self::SINGLE_STYLE;
        $this->setAllowRenameFiles(true);
        $this->setFilesDispersion(true);
        $result = $this->save($this->getDestinationDirectory($customerIdentifier, $entityType));
        unset($result['path']);

        $result['status'] = 'new';
        $result['name']   = substr($result['file'], strrpos($result['file'], '/') + 1);

        return $result;
    }

    public function getDestinationDirectory(
        int $customerIdentifier,
        string $entityType
    ): string {
        return $this->mediaDirectory->getAbsolutePath(
            $this->attachmentConfig->getBasePath($entityType, $customerIdentifier)
        );
    }
}
