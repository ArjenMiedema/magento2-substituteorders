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

namespace Dealer4Dealer\SubstituteOrders\Observer\Sales;

use Dealer4Dealer\SubstituteOrders\Api\OrderRepositoryInterface;
use Dealer4Dealer\SubstituteOrders\Model\OrderAddressFactory;
use Dealer4Dealer\SubstituteOrders\Model\OrderFactory;
use Dealer4Dealer\SubstituteOrders\Model\OrderItemFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\AttributeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order;

class OrderSaveAfter implements ObserverInterface
{
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly OrderFactory $orderFactory,
        private readonly OrderAddressFactory $addressFactory,
        private readonly OrderItemFactory $orderItemFactory,
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly CustomerRepositoryInterface $customerRepository
    ) {
    }

    public function execute(
        Observer $observer
    ): void {
        /** @var Order $order */
        $order = $observer->getData('order');
        try {
            $substitute = $this->orderRepository->getByMagentoOrderId($order->getId());
        } catch (LocalizedException) {
            $substitute = $this->orderFactory->create();
            $substitute->setMagentoOrderId($order->getId());
        }

        $payment = $order->getPayment()->getMethodInstance();
        $payment->setStore($order->getStoreId());

        $substitute->setPoNumber($order->getPoNumber());
        $substitute->setMagentoCustomerId($order->getCustomerId());
        $substitute->setBaseTaxAmount($order->getBaseTaxAmount());
        $substitute->setBaseDiscountAmount($order->getBaseDiscountAmount());
        $substitute->setBaseShippingAmount($order->getBaseShippingAmount());
        $substitute->setBaseSubtotal($order->getBaseSubtotal());
        $substitute->setBaseGrandTotal($order->getBaseGrandTotal());
        $substitute->setShippingMethod($order->getShippingDescription());
        $substitute->setTaxAmount($order->getTaxAmount());
        $substitute->setDiscountAmount($order->getDiscountAmount());
        $substitute->setShippingAmount($order->getShippingAmount());
        $substitute->setSubtotal($order->getSubtotal());
        $substitute->setGrandTotal($order->getGrandTotal());
        $substitute->setOrderDate($order->getCreatedAt());
        $substitute->setState($order->getState());
        $substitute->setPaymentMethod($payment->getTitle());
        $substitute->setUpdatedAt($order->getUpdatedAt());
        $substitute->setMagentoIncrementId($order->getIncrementId());


        # Add billing address
        $substituteBillingAddress = $substitute->getBillingAddress();
        if (!$substituteBillingAddress) {
            $substituteBillingAddress = $this->addressFactory->create();
        }

        $billingAddressData = $order->getBillingAddress()->getData();
        $billingAddressData['country'] = $billingAddressData['country_id'];

        $substituteBillingAddress->setData(array_merge($substituteBillingAddress->getData(), $billingAddressData));
        $substitute->setBillingAddress($substituteBillingAddress);

        # Add shipping address
        $substituteShippingAddress = $substitute->getShippingAddress();
        if (!$substituteShippingAddress) {
            $substituteShippingAddress = $this->addressFactory->create();
        }

        if ($order->getShippingAddress()) {
            $shippingAddressData = $order->getShippingAddress()->getData();
        } else {
            $shippingAddressData = $order->getBillingAddress()->getData();
        }

        $shippingAddressData['country'] = $shippingAddressData['country_id'];
        $substituteShippingAddress->setData(array_merge($substituteShippingAddress->getData(), $shippingAddressData));
        $substitute->setShippingAddress($substituteShippingAddress);

        if ($order->getCustomerId() !== 0 && $order->getCustomerId() !== null) {
            $customer = $this->customerRepository->getById($order->getCustomerId());
            /** @var AttributeInterface */
            $externalCustomerIdAttribute = $customer->getCustomAttribute("external_customer_id");
            if ($externalCustomerIdAttribute !== null && $externalCustomerIdAttribute->getValue() !== '') {
                $substitute->setExternalCustomerId($externalCustomerIdAttribute->getValue());
            }
        }

        # Add order items
        $items = [];
        foreach ($order->getAllVisibleItems() as $item) {
            if (!empty($item->getData('parent_item'))) {
                continue;
            }

            $substituteItem = $this->orderItemFactory->create();
            $substituteItem->setData($item->getData());
            $substituteItem->setData('qty', $item->getData('qty_ordered'));

            $items[] = $substituteItem;
        }

        $substitute->setItems($items);

        # save order
        $substitute->save();
    }
}
