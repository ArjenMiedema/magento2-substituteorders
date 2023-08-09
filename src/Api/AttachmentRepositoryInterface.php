<?php

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\AttachmentInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\AttachmentSearchResultsInterface;
use Magento\Framework\Api\SearchResults;

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
     * @return SearchResults
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResults;

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
     * @param int    $entityTypeIdentifier
     * @param int    $magentoCustomerIdentifier
     * @param string $entityType
     *
     * @return array
     */
    public function getAttachmentsByEntityTypeIdentifier(
        int $entityTypeIdentifier,
        int $magentoCustomerIdentifier,
        string $entityType
    ): array;
}
