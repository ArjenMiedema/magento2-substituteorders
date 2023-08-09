<?php

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Model;

use Dealer4Dealer\SubstituteOrders\Api\Data\InvoiceInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\OrderSearchResultsInterface;
use Dealer4Dealer\SubstituteOrders\Model\OrderFactory;
use Exception;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResults;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\DataObjectHelper;
use Dealer4Dealer\SubstituteOrders\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Dealer4Dealer\SubstituteOrders\Api\OrderRepositoryInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Dealer4Dealer\SubstituteOrders\Model\ResourceModel\Order as ResourceOrder;
use Dealer4Dealer\SubstituteOrders\Api\Data\OrderInterfaceFactory;
use Dealer4Dealer\SubstituteOrders\Api\Data\OrderSearchResultsInterfaceFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Dealer4Dealer\SubstituteOrders\Api\Data\OrderInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Dealer4Dealer\SubstituteOrders\Model\ResourceModel\Order\CollectionFactory;

use function Dealer4Dealer\SubstituteOrders\Model\__;

class OrderRepository implements OrderRepositoryInterface
{
    public function __construct(
        private readonly ResourceOrder $resource,
        private readonly OrderFactory $orderFactory,
        private readonly OrderInterfaceFactory $dataOrderFactory,
        private readonly OrderCollectionFactory $orderCollectionFactory,
        private readonly OrderSearchResultsInterfaceFactory $searchResultsFactory,
        private readonly DataObjectHelper $dataObjectHelper,
        private readonly DataObjectProcessor $dataObjectProcessor,
        private readonly StoreManagerInterface $storeManager,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly CollectionProcessorInterface $collectionProcessor
    ) {
    }

    /**
     * @throws CouldNotSaveException
     */
    public function save(OrderInterface $order): OrderInterface {
        try {
            $this->resource->save($order);
        } catch (Exception $exception) {
            throw new CouldNotSaveException(
                __(
                    'Could not save the order: %1',
                    $exception->getMessage()
                )
            );
        }

        return $order;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getById(int $orderId): OrderInterface
    {
        $order = $this->orderFactory->create();
        $order->load($orderId);
        if (!$order->getId()) {
            throw new NoSuchEntityException(__('Order with id "%1" does not exist.', $orderId));
        }

        return $order;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getByMagentoOrderId(int $id): OrderInterface
    {
        $order = $this->orderFactory->create();
        $order->load($id, "magento_order_id");
        if (!$order->getId()) {
            throw new NoSuchEntityException(__('Order with id "%1" does not exist.', $id));
        }

        return $order;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getByExtOrderId(int $id): OrderInterface
    {
        $order = $this->orderFactory->create();
        $order->load($id, "ext_order_id");
        if (!$order->getId()) {
            throw new NoSuchEntityException(__('External Order with id "%1" does not exist.', $id));
        }

        return $order;
    }

    public function getList(
        SearchCriteriaInterface $searchCriteria
    ): OrderSearchResultsInterface {
        $collection = $this->orderCollectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());

        return $searchResults;
    }

    /**
     * @throws CouldNotDeleteException
     */
    public function delete(
        OrderInterface $order
    ): bool {
        try {
            $this->resource->delete($order);
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(
                __(
                    'Could not delete the Order: %1',
                    $exception->getMessage()
                )
            );
        }

        return true;
    }

    public function deleteById(int $orderId): bool
    {
        return $this->delete($this->getById($orderId));
    }

    public function getOrdersByInvoice(InvoiceInterface $invoice): array
    {
        return $this->getList(
            $this->searchCriteriaBuilder->addFilter(
                'invoice_id',
                $invoice->getId()
            )->create()
        )->getItems();
    }

    public function getOrders(array $ids): array
    {
        return $this->getList(
            $this->searchCriteriaBuilder->addFilter(
                'order_id',
                $ids,
                'in'
            )->create()
        )->getItems();
    }
}
