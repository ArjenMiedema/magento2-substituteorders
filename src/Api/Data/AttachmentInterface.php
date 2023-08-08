<?php

namespace Dealer4Dealer\SubstituteOrders\Api\Data;

use Dealer4Dealer\SubstituteOrders\Api\Data\File\ContentInterface;

interface AttachmentInterface
{
    public const ENTITY_TYPE = 'entity_type',
        ENTITY_TYPE_IDENTIFIER = 'entity_type_identifier',
        FILE = 'file',
        MAGENTO_CUSTOMER_IDENTIFIER = 'magento_customer_identifier',
        ATTACHMENT_ID = 'attachment_id',
        FILE_CONTENT = 'file_content';

    public function getAttachmentId(): ?int;

    public function setAttachmentId(int $attachmentId): self;

    public function getMagentoCustomerIdentifier(): ?int;

    public function setMagentoCustomerIdentifier(int $customerId): self;

    public function getFile(): ?string;

    public function setFile(string $file);

    public function getEntityType(): ?string;

    public function setEntityType(string $entityType): self;

    public function getEntityTypeIdentifier(): ?string;

    public function setEntityTypeIdentifier(string $identifier): self;

    public function getFileContent(): ?ContentInterface;

    public function setFileContent(?ContentInterface $fileContent = null): self;
}
