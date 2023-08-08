<?php

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\AttachmentInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\AttachmentSearchResultsInterface;

interface AttachmentRepositoryInterface
{
    /**
     * @param AttachmentInterface $attachment
     *
     * @return AttachmentInterface
     */
    public function save(AttachmentInterface $attachment): AttachmentInterface;

    /**
     * @param int $attachmentId
     *
     * @return AttachmentInterface
     */
    public function getById(int $attachmentId): AttachmentInterface;

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return AttachmentSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): AttachmentSearchResultsInterface;

    /**
     * @param AttachmentInterface $attachment
     *
     * @return bool
     */
    public function delete(AttachmentInterface $attachment): bool;

    /**
     * @param int $attachmentId
     *
     * @return bool
     */
    public function deleteById(int $attachmentId): bool;

    /**
     * @param string $entityTypeIdentifier
     * @param string $magentoCustomerIdentifier
     * @param string $entityType
     *
     * @return AttachmentSearchResultsInterface
     */
    public function getAttachmentsByEntityTypeIdentifier(
        string $entityTypeIdentifier,
        string $magentoCustomerIdentifier,
        string $entityType
    ): AttachmentSearchResultsInterface;
}
