<?php

/**
 * A Magento 2 module named Dealer4Dealer\SubstituteOrders
 * Copyright (C) 2017 Maikel Martens
 *
 * This file is part of Dealer4Dealer\SubstituteOrders.
 *
 * Dealer4Dealer\SubstituteOrders is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Dealer4Dealer\SubstituteOrders\Block\Order;

use function Dealer4Dealer\SubstituteOrders\Block\Order\__;

/**
 * Substitute Orders history block
 */
class History extends \Magento\Framework\View\Element\Template
{
    const DEFAULT_PAGE_SIZE = 10;

    /** @var string */
    protected $_template = 'order/history.phtml';

    /** @var \Dealer4Dealer\SubstituteOrders\Model\ResourceModel\Order\CollectionFactory */
    protected $orderCollectionFactory;

    /** @var \Magento\Customer\Model\Session */
    protected $customerSession;

    /** @var \Magento\Customer\Api\CustomerRepositoryInterface */
    protected $_customerRepository;

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface  */
    protected $_scopeConfig;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Dealer4Dealer\SubstituteOrders\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        array $data = []
    ) {
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->customerSession = $customerSession;
        $this->_customerRepository = $customerRepository;

        $this->_scopeConfig = $context->getScopeConfig();

        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->pageConfig->getTitle()->set(__('Orders'));

        if ($this->getOrderCollection()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'dealer4dealer.orders.pager'
            );

            $pager->setAvailableLimit([10 => 10, 15 => 15, 20 => 20])
                ->setShowPerPage(true)
                ->setCollection($this->getOrderCollection());

            $this->setChild('pager', $pager);
            $this->getOrderCollection()->load();
        }
    }

    public function getOrderCollection()
    {
        /** @var int */
        $magentoCustomerId = $this->customerSession->getCustomer()->getid();
        $magentoCustomer = $this->_customerRepository->getById($magentoCustomerId);

        /* @var $collection \Dealer4Dealer\SubstituteOrders\Model\ResourceModel\Order\Collection */
        $collection = $this->orderCollectionFactory->create();

        if ($this->getRequest()->getParam('date_from')) {
            $collection->addFieldToFilter(
                'order_date',
                ['gteq' => date('Y-m-d', strtotime($this->getRequest()->getParam('date_from'))) . ' 00:00:00']
            );
        }

        if ($this->getRequest()->getParam('date_to')) {
            $collection->addFieldToFilter(
                'order_date',
                ['lteq' => date('Y-m-d', strtotime($this->getRequest()->getParam('date_to'))) . ' 23:59:59']
            );
        }

        if ($this->getRequest()->getParam('q')) {
            $query = $this->getRequest()->getParam('q');

            $collection->addFieldToFilter(
                [
                    'po_number',
                    'magento_increment_id',
                    'ext_order_id'
                ],
                [
                    ['like' => '%' . $query . '%'],
                    ['like' => '%' . $query . '%'],
                    ['like' => '%' . $query . '%']
                ]
            );
        }

        /** @var \Magento\Framework\Api\AttributeInterface */
        $externalCustomerIdAttribute = $magentoCustomer->getCustomAttribute("external_customer_id");
        $selectOrderBySetting = $this->_scopeConfig->getValue(
            'substitute/general/select_orders_by',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $customerSelectionId = $magentoCustomerId;
        if ($selectOrderBySetting === 'external_customer_id' && $externalCustomerIdAttribute !== null && $externalCustomerIdAttribute->getValue() !== '') {
            $customerSelectionId = $externalCustomerIdAttribute->getValue();
            $collection->addFieldToFilter($selectOrderBySetting, $customerSelectionId);
        } else {
            $collection->addFieldToFilter('magento_customer_id', $customerSelectionId);
        }

        $collection->setOrder('order_date')
            ->setOrder('magento_increment_id')
            ->setPageSize($this->getPageSize())
            ->setCurPage($this->getCurrentPage());

        return $collection;
    }

    public function getCurrentPage()
    {
        return $this->getRequest()->getParam('p', 1);
    }

    public function getPageSize()
    {
        return $this->getRequest()->getParam('limit', self::DEFAULT_PAGE_SIZE);
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getViewUrl($order)
    {
        return $this->getUrl('*/*/view', ['id' => $order->getId()]);
    }

    public function getReorderUrl($order)
    {
        return $this->getUrl('*/*/reorder', ['id' => $order->getId()]);
    }
}
