<?php

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\AttachmentInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\AttachmentSearchResultsInterface;
use Magento\Framework\Api\SearchResults;

interface AttachmentRepositoryInterface
{
    public function save(AttachmentInterface $attachment): AttachmentInterface;

    public function getById(int $attachmentId): AttachmentInterface;

    public function getList(SearchCriteriaInterface $searchCriteria): SearchResults;

    public function delete(AttachmentInterface $attachment): bool;

    public function deleteById(int $attachmentId): bool;

    public function getAttachmentsByEntityTypeIdentifier(
        int $entityTypeIdentifier,
        int $magentoCustomerIdentifier,
        string $entityType
    ): AttachmentSearchResultsInterface;
}
