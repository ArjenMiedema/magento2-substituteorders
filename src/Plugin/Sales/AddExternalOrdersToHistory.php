<?php

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Plugin\Sales;

use Dealer4Dealer\SubstituteOrders\Api\Data\OrderInterface;
use Dealer4Dealer\SubstituteOrders\Model\OrderRepository;
use Magento\Customer\Model\Session;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Block\Order\History;
use Magento\Sales\Model\ResourceModel\Order\Collection;

class AddExternalOrdersToHistory
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly Session $customerSession
    ) {
    }

    public function afterGetOrders(
        History $subject,
        bool|Collection $result
    ): bool|Collection {
        // We only get `false` as result when there is no customer logged in
        if ($result === false) {
            return false;
        }

        $collection = $this->orderRepository->getList(
            $this->searchCriteriaBuilder
                ->addFilter(
                    OrderInterface::MAGENTO_CUSTOMER_ID,
                    $this->customerSession->getCustomerId()
                )
                ->create()
        );

        foreach ($collection->getItems() as $item) {
            $result->addItem($item);
        }

        return $result;
    }
}
