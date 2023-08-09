<?php

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Model;

use Dealer4Dealer\SubstituteOrders\Api\Data\OrderSearchResultsInterface;
use Dealer4Dealer\SubstituteOrders\Model\OrderFactory;
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
    /*
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /*
     * @var OrderCollectionFactory
     */
    protected $orderCollectionFactory;

    /*
     * @var OrderInterfaceFactory
     */
    protected $dataOrderFactory;

    /*
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /*
     * @var OrderFactory
     */
    protected $orderFactory;

    /*
     * @var ResourceOrder
     */
    protected $resource;

    /*
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /*
     * @var OrderSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /*
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @param ResourceOrder $resource
     * @param OrderFactory $orderFactory
     * @param OrderInterfaceFactory $dataOrderFactory
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param OrderSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceOrder $resource,
        OrderFactory $orderFactory,
        OrderInterfaceFactory $dataOrderFactory,
        OrderCollectionFactory $orderCollectionFactory,
        OrderSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->orderFactory = $orderFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataOrderFactory = $dataOrderFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function save(OrderInterface $order): OrderInterface {
        try {
            $this->resource->save($order);
        } catch (\Exception $exception) {
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
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
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

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    ): \Magento\Framework\Api\SearchResults {
        $collection = $this->orderCollectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());

        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Dealer4Dealer\SubstituteOrders\Api\Data\OrderInterface $order
    ): bool {
        try {
            $this->resource->delete($order);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(
                __(
                    'Could not delete the Order: %1',
                    $exception->getMessage()
                )
            );
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById(int $orderId): bool
    {
        return $this->delete($this->getById($orderId));
    }

    public function getOrdersByInvoice($invoice)
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('invoice_id', $invoice->getId(), 'eq')->create();
        $results = $this->getList($searchCriteria);

        return $results->getItems();
    }

    public function getOrders($ids)
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('order_id', $ids, 'in')->create();
        $results = $this->getList($searchCriteria);

        return $results->getItems();
    }
}
