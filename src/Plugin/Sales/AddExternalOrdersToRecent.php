<?php

declare(strict_types=1);

// phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
// phpcs:disable Generic.NamingConventions.CamelCapsFunctionName.ScopeNotCamelCaps

namespace Dealer4Dealer\SubstituteOrders\Plugin\Sales;

use Dealer4Dealer\SubstituteOrders\Api\Data\OrderInterface;
use Dealer4Dealer\SubstituteOrders\Model\OrderRepository;
use Magento\Customer\Model\Session;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Block\Order\History;
use Magento\Sales\Block\Order\Recent;
use Magento\Sales\Model\ResourceModel\Order\Collection;

class AddExternalOrdersToRecent
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly Session $customerSession
    ) {
    }

    public function after__call(
        Recent $subject,
        mixed $result,
        string $method,
        array $args
    ) {
        if ($method !== 'setOrders') {
            return $result;
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
