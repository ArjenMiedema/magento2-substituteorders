<?php

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Block\Invoice;

use Dealer4Dealer\SubstituteOrders\Api\Data\OrderInterface;
use Dealer4Dealer\SubstituteOrders\Api\InvoiceRepositoryInterface;
use Dealer4Dealer\SubstituteOrders\Model\ResourceModel\Invoice\CollectionFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\Data\Collection;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Theme\Block\Html\Pager;

/**
 * Substitute Invoice history block
 */
class Invoice extends Template
{
    private const DEFAULT_PAGE_SIZE = 10;

    protected $_template = 'order/history.phtml';

    public function __construct(
        Context $context,
        private readonly CollectionFactory $invoiceCollectionFactory,
        private readonly Session $customerSession,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    protected function _prepareLayout(): void
    {
        parent::_prepareLayout();

        $this->pageConfig
            ->getTitle()
            ->set(__('Orders'));

        if ($this->getInvoiceCollection()) {
            $pager = $this->getLayout()->createBlock(
                Pager::class,
                'dealer4dealer.orders.pager'
            );

            $pager->setAvailableLimit([10 => 10, 15 => 15, 20 => 20])
                ->setShowPerPage(true)
                ->setCollection($this->getInvoiceCollection());

            $this->setChild('pager', $pager);
            $this->getInvoiceCollection()->load();
        }
    }

    public function getInvoiceCollection(): Collection
    {
        $customerId = $this->customerSession->getCustomerId();

        /** @var InvoiceRepositoryInterface&Collection $collection */
        $collection = $this->invoiceCollectionFactory->create();
        $collection
            ->addFieldToFilter('magento_customer_id', $customerId)
            ->setPageSize($this->getPageSize())
            ->setCurPage($this->getCurrentPage());

        return $collection;
    }

    public function getCurrentPage(): int
    {
        return (int) $this->getRequest()->getParam('p', 1);
    }

    public function getPageSize(): int
    {
        return (int) $this->getRequest()->getParam('limit', self::DEFAULT_PAGE_SIZE);
    }

    public function getPagerHtml(): string
    {
        return $this->getChildHtml('pager');
    }

    public function getViewUrl(OrderInterface $order): string
    {
        return $this->getUrl('*/*/view', ['id' => $order->getId()]);
    }

    public function getReorderUrl(OrderInterface $order): string
    {
        return $this->getUrl('*/*/reorder', ['id' => $order->getId()]);
    }
}
