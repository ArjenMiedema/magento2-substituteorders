<?php

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Observer\Sales;

use Dealer4Dealer\SubstituteOrders\Api\Data\InvoiceInterface;
use Dealer4Dealer\SubstituteOrders\Api\InvoiceRepositoryInterface;
use Dealer4Dealer\SubstituteOrders\Model\InvoiceFactory;
use Dealer4Dealer\SubstituteOrders\Model\InvoiceItemFactory;
use Dealer4Dealer\SubstituteOrders\Model\OrderAddressFactory;
use Dealer4Dealer\SubstituteOrders\Model\OrderFactory;
use Exception;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class OrderInvoiceSaveAfter implements ObserverInterface
{

    public function __construct(
        private readonly InvoiceFactory $invoiceFactory,
        private readonly OrderAddressFactory $addressFactory,
        private readonly InvoiceItemFactory $invoiceItemFactory,
        private readonly InvoiceRepositoryInterface $invoiceRepository,
        private readonly OrderFactory $orderFactory
    ) {
    }

    public function execute(
        Observer $observer
    ): void {
        $invoice = $observer->getData('invoice');

        try {
            $substitute = $this->invoiceRepository->getByMagentoInvoiceId($invoice->getId());
        } catch (LocalizedException $e) {
            $substitute = $this->invoiceFactory->create();
            $substitute->setMagentoInvoiceId($invoice->getId());
        }

        $order = $this->orderFactory->create()->load($invoice->getOrderId(), 'magento_order_id');

        $substitute->setPoNumber($invoice->getPoNumber());
        $substitute->setMagentoCustomerId($invoice->getCustomerId());
        $substitute->setBaseTaxAmount($invoice->getBaseTaxAmount());
        $substitute->setBaseDiscountAmount($invoice->getBaseDiscountAmount());
        $substitute->setBaseShippingAmount($invoice->getBaseShippingAmount());
        $substitute->setBaseSubtotal($invoice->getBaseSubtotal());
        $substitute->setBaseGrandTotal($invoice->getBaseGrandTotal());
        $substitute->setTaxAmount($invoice->getTaxAmount());
        $substitute->setDiscountAmount($invoice->getDiscountAmount());
        $substitute->setShippingAmount($invoice->getShippingAmount());
        $substitute->setSubtotal($invoice->getSubtotal());
        $substitute->setGrandtotal($invoice->getGrandTotal());
        $substitute->setInvoiceDate($invoice->getCreatedAt());
        $substitute->setState($invoice->getState());
        $substitute->setUpdatedAt($invoice->getUpdatedAt());
        $substitute->setMagentoIncrementId($invoice->getIncrementId());


        # Add billing address
        $substituteBillingAddress = $substitute->getBillingAddress();
        if (!$substituteBillingAddress) {
            $substituteBillingAddress = $this->addressFactory->create();
        }

        $billingAddressData = $invoice->getBillingAddress()->getData();
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

        $substituteShippingAddress->setData(array_merge($substituteShippingAddress->getData(), $shippingAddressData));
        $substitute->setShippingAddress($substituteShippingAddress);


        # Add order items
        $items = [];
        foreach ($invoice->getItems() as $item) {
            if (!empty($item->getData('parent_item'))) {
                continue;
            }

            $substituteItem = $this->invoiceItemFactory->create();
            $substituteItem->setData($item->getData());
            $substituteItem->setOrderId($order->getId());

            $items[] = $substituteItem;
        }

        $substitute->setItems($items);

        $orderIds = $substitute->getOrderIds();
        if (is_array($orderIds)) {
            $orderIds[] = $order->getId();
        } else {
            $orderIds = [$order->getId()];
        }

        $substitute->setOrderIds($orderIds);

        # save invoice
        $substitute->save();
    }
}
