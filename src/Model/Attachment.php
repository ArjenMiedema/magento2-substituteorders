<?php

namespace Dealer4Dealer\SubstituteOrders\Model;

use Dealer4Dealer\SubstituteOrders\Api\Data\AttachmentInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\File\ContentInterface;
use Dealer4Dealer\SubstituteOrders\Model\File\Content;
use Dealer4Dealer\SubstituteOrders\Model\ResourceModel\Attachment as ResourceModel;
use Magento\Framework\Model\AbstractModel;

class Attachment extends AbstractModel implements AttachmentInterface
{
    public const ENTITY = 'attachment';

    /**
     * @var string
     */
    protected $_eventPrefix = 'substitute_order_attachment';

    /**
     * @var string
     */
    protected $_eventObject = 'attachment';

    protected function _construct(): void
    {
        $this->_init(ResourceModel::class);
    }

    public function getAttachmentId(): int
    {
        return (int) $this->getData(self::ATTACHMENT_ID);
    }

    public function setAttachmentId(int $attachmentId): self
    {
        return $this->setData(self::ATTACHMENT_ID, $attachmentId);
    }

    public function getMagentoCustomerIdentifier(): int
    {
        return (int) $this->getData(self::MAGENTO_CUSTOMER_IDENTIFIER);
    }

    public function setMagentoCustomerIdentifier(int $customerId): self
    {
        return $this->setData(self::MAGENTO_CUSTOMER_IDENTIFIER, $customerId);
    }

    public function getFile(): string
    {
        return $this->getData(self::FILE);
    }

    public function setFile(string $file): self
    {
        return $this->setData(self::FILE, $file);
    }

    public function getEntityType(): string
    {
        return $this->getData(self::ENTITY_TYPE);
    }

    public function setEntityType(string $entityType): self
    {
        return $this->setData(self::ENTITY_TYPE, $entityType);
    }

    public function getEntityTypeIdentifier(): int
    {
        return (int) $this->getData(self::ENTITY_TYPE_IDENTIFIER);
    }

    public function setEntityTypeIdentifier(int $identifier): self
    {
        return $this->setData(self::ENTITY_TYPE_IDENTIFIER, $identifier);
    }

    public function setFileContent(ContentInterface $fileContent = null): self
    {
        return $this->setData(self::FILE_CONTENT, $fileContent);
    }

    public function getFileContent(): ?ContentInterface
    {
        return $this->getData(self::FILE_CONTENT);
    }

    public function getBasePath(string $entityType, int $customerIdentifier = 0): string
    {
        return sprintf(
            'customer/substitute_order/files/%d/%s',
            $customerIdentifier,
            $entityType
        );
    }
}
