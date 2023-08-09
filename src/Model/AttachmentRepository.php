<?php

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Model;

use Dealer4Dealer\SubstituteOrders\Api\Data\AttachmentInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\AttachmentSearchResultsInterfaceFactory;
use Dealer4Dealer\SubstituteOrders\Model\File\ContentValidator;
use Dealer4Dealer\SubstituteOrders\Model\ResourceModel\Attachment\Collection;
use Dealer4Dealer\SubstituteOrders\Model\ResourceModel\Attachment\CollectionFactory;
use Exception;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResults;
use Magento\Framework\Exception\CouldNotSaveException;
use Dealer4Dealer\SubstituteOrders\Api\AttachmentRepositoryInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Dealer4Dealer\SubstituteOrders\Model\ResourceModel\Attachment as ResourceAttachment;
use Magento\Framework\Exception\CouldNotDeleteException;
use Dealer4Dealer\SubstituteOrders\Model\ResourceModel\Attachment\CollectionFactory as AttachmentCollectionFactory;
use Dealer4Dealer\SubstituteOrders\Api\Data\AttachmentInterfaceFactory;
use Dealer4Dealer\SubstituteOrders\Api\Data\File\ContentUploaderInterface;

class AttachmentRepository implements AttachmentRepositoryInterface
{
    public function __construct(
        private readonly ResourceAttachment $resource,
        private readonly AttachmentFactory $attachmentFactory,
        private readonly ContentUploaderInterface $fileContentUploader,
        private readonly ContentValidator $contentValidator,
        private readonly CollectionFactory $collectionFactory,
        private readonly AttachmentSearchResultsInterfaceFactory $searchResultFactory,
        private readonly CollectionProcessorInterface $collectionProcessor
    ) {
    }

    /**
     * @throws CouldNotSaveException
     * @throws InputException
     */
    public function save(AttachmentInterface $attachment): AttachmentInterface
    {
        $this->contentValidator->isValid($attachment->getFileContent());

        $result = $this->fileContentUploader->upload(
            $attachment->getFileContent(),
            $attachment->getMagentoCustomerIdentifier(),
            $attachment->getEntityType()
        );

        $attachment->setFile($result['file']);

        try {
            $this->resource->save($attachment);
        } catch (Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save the attachment: %1', $exception->getMessage())
            );
        }

        return $attachment;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getById(int $attachmentId): AttachmentInterface
    {
        $attachment = $this->attachmentFactory->create();
        $this->resource->load($attachment, $attachmentId);

        if (!$attachment->getId()) {
            throw new NoSuchEntityException(__('Attachment with id "%1" does not exist.', $attachmentId));
        }

        return $attachment;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        SearchCriteriaInterface $searchCriteria
    ): SearchResults {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResults = $this->searchResultFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());

        return $searchResults;

    }

    /**
     * @throws CouldNotDeleteException
     */
    public function delete(AttachmentInterface $attachment): bool
    {
        $attachmentFilePath = $this->fileContentUploader->getDestinationDirectory(
            $attachment->getMagentoCustomerIdentifier(),
            $attachment->getEntityType()
        ) . $attachment->getFile();

        if (file_exists($attachmentFilePath)) {
            unlink($attachmentFilePath);
        }

        try {
            $this->resource->delete($attachment);
        } catch (Exception $e) {
            throw new CouldNotDeleteException(__('Could not delete the Attachment: %1', $e->getMessage()));
        }

        return true;
    }

    public function deleteById(int $attachmentId): bool
    {
        return $this->delete($this->getById($attachmentId));
    }

    public function saveAttachmentByEntityType(
        string $entityType,
        int $entityTypeIdentifier,
        int $magentoCustomerIdentifier,
        array $fileContent
    ): void {
        $this->deleteAttachmentsByEntityTypeIdentifier(
            $entityTypeIdentifier,
            $magentoCustomerIdentifier,
            $entityType
        );

        foreach ($fileContent as $file) {
            /* @var $attachment \Dealer4Dealer\SubstituteOrders\Model\Attachment */
            $attachment = $this->attachmentFactory->create();
            $attachment->setFileContent($file);
            $attachment->setMagentoCustomerIdentifier($magentoCustomerIdentifier);
            $attachment->setEntityType($entityType);
            $attachment->setEntityTypeIdentifier($entityTypeIdentifier);

            $this->save($attachment);
        }
    }

    public function deleteAttachmentsByEntityTypeIdentifier(
        int $entityTypeIdentifier,
        int $magentoCustomerIdentifier,
        string $entityType = 'order'
    ): void {
        $attachments = $this->getAttachmentsByEntityTypeIdentifier(
            $entityTypeIdentifier,
            $magentoCustomerIdentifier,
            $entityType
        );

        /* @var Attachment $attachment */
        foreach ($attachments as $attachment) {
            $this->delete($attachment);
        }
    }

    public function getAttachmentsByEntityTypeIdentifier(
        int $entityTypeIdentifier,
        int $magentoCustomerIdentifier,
        string $entityType = 'order'
    ) {
        return $this->getList(
            $this->searchCriteriaBuilder
                ->addFilter(AttachmentInterface::ENTITY_TYPE_IDENTIFIER, $entityTypeIdentifier)
                ->addFilter(AttachmentInterface::ENTITY_TYPE, $entityType)
                ->addFilter(AttachmentInterface::MAGENTO_CUSTOMER_IDENTIFIER, $magentoCustomerIdentifier)
                ->create()
        )->getItems();
    }
}
