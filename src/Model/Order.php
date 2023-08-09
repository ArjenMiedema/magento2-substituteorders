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

namespace Dealer4Dealer\SubstituteOrders\Model;

use Dealer4Dealer\SubstituteOrders\Api\Data\OrderAddressInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\OrderInterface;
use Dealer4Dealer\SubstituteOrders\Api\ShipmentRepositoryInterface;
use Dealer4Dealer\SubstituteOrders\Model\AdditionalData;

class Order extends \Magento\Framework\Model\AbstractModel implements OrderInterface
{
    /**
     * @var string
     */
    const ENTITY = 'order';

    /**
     * @var string
     */
    protected $_eventPrefix = 'substitute_order_order';

    /**
     * @var string
     */
    protected $_eventObject = 'order';

    /*
     * @var \Dealer4Dealer\SubstituteOrders\Api\OrderAddressRepositoryInterface
     */
    protected $addressRepository;

    /*
     * @var \Dealer4Dealer\SubstituteOrders\Api\OrderItemRepositoryInterface
     */
    protected $itemRepository;

    /*
     * @var \Dealer4Dealer\SubstituteOrders\Model\ResourceModel\Invoice\CollectionFactory
     */
    protected $invoiceCollectionFactory;

    /*
     * @var \Dealer4Dealer\SubstituteOrders\Model\OrderInvoiceRelationFactory
     */
    protected $orderInvoiceRelationFactory;

    /*
     * @var \Dealer4Dealer\SubstituteOrders\Api\AttachmentRepositoryInterface
     */
    protected $attachmentRepository;

    /*
     * @var \Dealer4Dealer\SubstituteOrders\Api\ShipmentRepositoryInterface
     */
    protected $shipmentRepository;

    /*
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /*
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    protected $_items = null;
    protected $_billingAddress = null;
    protected $_shippingAddress = null;
    protected $_additionalData = null;
    protected $_attachments = null;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Dealer4Dealer\SubstituteOrders\Api\OrderItemRepositoryInterface $orderItems,
        \Dealer4Dealer\SubstituteOrders\Api\OrderAddressRepositoryInterface $orderAddress,
        \Dealer4Dealer\SubstituteOrders\Model\ResourceModel\Invoice\CollectionFactory $invoiceCollectionFactory,
        \Dealer4Dealer\SubstituteOrders\Model\OrderInvoiceRelationFactory $orderInvoiceRelationFactory,
        \Dealer4Dealer\SubstituteOrders\Api\ShipmentRepositoryInterface $shipmentRepository,
        \Dealer4Dealer\SubstituteOrders\Api\AttachmentRepositoryInterface $attachmentRepository,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->itemRepository = $orderItems;
        $this->addressRepository = $orderAddress;
        $this->invoiceCollectionFactory = $invoiceCollectionFactory;
        $this->orderInvoiceRelationFactory = $orderInvoiceRelationFactory;
        $this->shipmentRepository = $shipmentRepository;
        $this->attachmentRepository = $attachmentRepository;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Dealer4Dealer\SubstituteOrders\Model\ResourceModel\Order');
    }

    public function save()
    {
        if ($this->_additionalData) {
            $data = [];
            foreach ($this->_additionalData as $value) {
                $data[$value->getKey()] = $value->getValue();
            }

            $this->setData(self::ADDITIONAL_DATA, json_encode($data));
        }


        if ($this->_shippingAddress) {
            if (!$this->getData(self::SHIPPING_ADDRESS_ID) && $this->_shippingAddress->getId() != $this->getData(self::SHIPPING_ADDRESS_ID)) {
                try {
                    $oldAddress = $this->addressRepository->getById($this->getData(self::SHIPPING_ADDRESS_ID));
                    $this->_shippingAddress->setData(
                        array_merge($oldAddress->getData(), $this->_shippingAddress->getData())
                    );
                } catch (\Exception $e) { // @codingStandardsIgnoreLine

                }

                $this->_shippingAddress->setId($this->getData(self::SHIPPING_ADDRESS_ID));
            }

            $this->_shippingAddress->save();
            $this->setData(self::SHIPPING_ADDRESS_ID, $this->_shippingAddress->getId());
        }

        if ($this->_billingAddress) {
            if (!$this->getData(self::BILLING_ADDRESS_ID) && $this->_billingAddress->getId() != $this->getData(self::BILLING_ADDRESS_ID)) {
                try {
                    $oldAddress = $this->addressRepository->getById($this->getData(self::BILLING_ADDRESS_ID));
                    $this->_billingAddress->setData(
                        array_merge($oldAddress->getData(), $this->_billingAddress->getData())
                    );
                } catch (\Exception $e) { // @codingStandardsIgnoreLine

                }

                $this->_billingAddress->setId($this->getData(self::BILLING_ADDRESS_ID));
            }

            $this->_billingAddress->save();
            $this->setData(self::BILLING_ADDRESS_ID, $this->_billingAddress->getId());
        }

        parent::save();

        if ($this->getData(self::INVOICE_IDS)) {
            $activeIds = [];
            $collection = $this->orderInvoiceRelationFactory->create()->getCollection()
                ->addFieldToFilter('order_id', $this->getId());

            foreach ($collection as $relation) {
                if (in_array($relation->getInvoiceId(), $this->getData(self::INVOICE_IDS))) {
                    $activeIds[$relation->getInvoiceId()] = true;
                } else {
                    $relation->delete();
                }
            }

            foreach ($this->getData(self::INVOICE_IDS) as $invoiceId) {
                if (!isset($activeIds[$invoiceId])) {
                    $relation = $this->orderInvoiceRelationFactory->create();
                    $relation->setData(
                        [
                        'order_id' => $this->getId(),
                        'invoice_id' => $invoiceId
                        ]
                    );
                    $relation->save();
                }
            }
        }

        if ($this->_items) {
            $oldItems = $this->itemRepository->getOrderItems($this->getId());
            $oldSkus = [];
            $newSkus = [];
            foreach ($oldItems as $item) {
                $oldSkus[$item->getSku()] = $item;
            }

            foreach ($this->_items as $item) {
                $oldItem = isset($oldSkus[$item->getSku()]) ? $oldSkus[$item->getSku()] : null;

                if ($oldItem && $oldItem->getOrderId() == $this->getId()) {
                    $item->setData(array_merge($oldItem->getData(), $item->getData()));
                    $item->setId($oldItem->getId());
                } else {
                    $item->setId(null);
                }

                $item->setOrderId($this->getId());
                $item->save();

                $newSkus[$item->getSku()] = true;
            }

            foreach ($oldItems as $item) {
                if (!isset($newSkus[$item->getSku()])) {
                    $item->delete();
                }
            }
        }

        return $this;
    }

    public function delete()
    {
        if ($this->getShippingAddress()) {
            $this->getShippingAddress()->delete();
        }

        if ($this->getBillingAddress()) {
            $this->getBillingAddress()->delete();
        }

        if ($this->getItems()) {
            foreach ($this->getItems() as $item) {
                $item->delete();
            }
        }

        return parent::delete();
    }

    public function getRealOrderId()
    {
        $realOrderIdSetting = $this->scopeConfig->getValue(
            'substitute/general/real_order_id',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId()
        );

        if ($realOrderIdSetting == 'external') {
            $orderId = $this->getData('ext_order_id');
        } else {
            $orderId = $this->getData('magento_increment_id') ? $this->getData('magento_increment_id') : $this->getData('ext_order_id');
        }

        return $orderId ? $orderId : '-';
    }

    public function canShowBothIds()
    {
        return $this->scopeConfig->getValue(
            'substitute/general/show_both_ids',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId()
        );
    }

    public function getInvoices()
    {
        if (!$this->getId()) {
            return [];
        }

        return $this->invoiceCollectionFactory->create()->filterByOrder($this);
    }

    public function getShipmentCollection()
    {
        return $this->shipmentRepository->getShipmentsByOrder($this);
    }

    public function getAttachmentCollection()
    {
        return $this->attachmentRepository->getAttachmentsByEntityTypeIdentifier(
            $this->getOrderId(),
            $this->getMagentoCustomerId(),
            self::ENTITY
        );
    }

    /**
     * @inheritDoc
     */
    public function getOrderId(): int
    {
        return (int)  $this->getData(self::ORDER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setOrderId(int $orderId): self
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * @inheritDoc
     */
    public function getInvoiceIds(): ?array
    {
        if ($this->getData(self::INVOICE_IDS)) {
            return $this->getData(self::INVOICE_IDS);
        }

        $ids = [];
        foreach ($this->getInvoices() as $invoice) {
            $ids[] = $invoice->getId();
        }

        return $ids;
    }

    /**
     * @inheritDoc
     */
    public function setInvoiceIds(array $invoiceIds): self
    {
        return $this->setData(self::INVOICE_IDS, array_unique($invoiceIds));
    }

    /**
     * @inheritDoc
     */
    public function getMagentoOrderId(): ?int
    {
        return (int) $this->getData(self::MAGENTO_ORDER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setMagentoOrderId(int $magentoOrderid): self
    {
        return $this->setData(self::MAGENTO_ORDER_ID, $magento_order_id);
    }

    /**
     * @inheritDoc
     */
    public function getMagentoCustomerId(): ?int
    {
        return (int) $this->getData(self::MAGENTO_CUSTOMER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setMagentoCustomerId(int $magentoCustomerId): self
    {
        return $this->setData(self::MAGENTO_CUSTOMER_ID, $magento_customer_id);
    }

    /**
     * {@inheritDoc}
     * @see \Dealer4Dealer\SubstituteOrders\Api\Data\OrderInterface::getExternalCustomerId()
     */
    public function getExternalCustomerId(): int
    {
        return (int) $this->getData(self::EXTERNAL_CUSTOMER_ID);
    }

    /**
     * {@inheritDoc}
     * @see \Dealer4Dealer\SubstituteOrders\Api\Data\OrderInterface::setExternalCustomerId()
     */
    public function setExternalCustomerId($externalCustomerId): self
    {
        return $this->setData(self::EXTERNAL_CUSTOMER_ID, $external_customer_id);
    }

    /**
     * @inheritDoc
     */
    public function getExtOrderId(): ?string
    {
        return $this->getData(self::EXT_ORDER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setExtOrderId($extOrderId): self
    {
        return $this->setData(self::EXT_ORDER_ID, $extOrderId);
    }

    /**
     * @inheritDoc
     */
    public function getBaseGrandtotal(): ?float
    {
        return (float) $this->getData(self::BASE_GRANDTOTAL);
    }

    /**
     * @inheritDoc
     */
    public function setBaseGrandtotal(float $baseGrandtotal): self
    {
        return $this->setData(self::BASE_GRANDTOTAL, $baseGrandtotal);
    }

    /**
     * @inheritDoc
     */
    public function getBaseSubtotal(): ?float
    {
        return (float)$this->getData(self::BASE_SUBTOTAL);
    }

    /**
     * @inheritDoc
     */
    public function setBaseSubtotal(float $baseSubtotal): self
    {
        return $this->setData(self::BASE_SUBTOTAL, $baseSubtotal);
    }

    /**
     * @inheritDoc
     */
    public function getGrandtotal(): float
    {
        return (float)$this->getData(self::GRANDTOTAL);
    }

    /**
     * @inheritDoc
     */
    public function setGrandtotal(float $grandtotal): self
    {
        return $this->setData(self::GRANDTOTAL, $grandtotal);
    }

    /**
     * @inheritDoc
     */
    public function getSubtotal(): float
    {
        return (float)$this->getData(self::SUBTOTAL);
    }

    /**
     * @inheritDoc
     */
    public function setSubtotal(float $subtotal): self
    {
        return $this->setData(self::SUBTOTAL, $subtotal);
    }

    /**
     * @inheritDoc
     */
    public function getPoNumber(): string
    {
        return $this->getData(self::PO_NUMBER);
    }

    /**
     * @inheritDoc
     */
    public function setPoNumber($poNumber): self
    {
        return $this->setData(self::PO_NUMBER, $poNumber);
    }

    /**
     * @inheritDoc
     */
    public function getState(): string
    {
        return $this->getData(self::STATE);
    }

    /**
     * @inheritDoc
     */
    public function setState(string $state): self
    {
        return $this->setData(self::STATE, $state);
    }

    /**
     * @inheritDoc
     */
    public function getShippingMethod(): string
    {
        return $this->getData(self::SHIPPING_METHOD);
    }

    /**
     * @inheritDoc
     */
    public function setShippingMethod($shippingMethod): self
    {
        return $this->setData(self::SHIPPING_METHOD, $shippingMethod);
    }

    /**
     * @inheritDoc
     */
    public function getShippingAddress(): ?OrderAddressInterface
    {
        if (!$this->_shippingAddress) {
            try {
                $this->_shippingAddress = $this->addressRepository->getById($this->getData(self::SHIPPING_ADDRESS_ID));
            } catch (\Exception) {

            }
        }

        return $this->_shippingAddress;
    }

    /**
     * @inheritDoc
     */
    public function setShippingAddress(\Dealer4Dealer\SubstituteOrders\Api\Data\OrderAddressInterface $shippingAddress): self
    {
        $this->_shippingAddress = $shippingAddress;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBillingAddress(): OrderAddressInterface
    {
        if (!$this->_billingAddress) {
            try {
                $this->_billingAddress = $this->addressRepository->getById($this->getData(self::BILLING_ADDRESS_ID));
            } catch (\Exception $e) { // @codingStandardsIgnoreLine

            }
        }

        return $this->_billingAddress;
    }

    /**
     * @inheritDoc
     */
    public function setBillingAddress(\Dealer4Dealer\SubstituteOrders\Api\Data\OrderAddressInterface $billing_address): self
    {
        $this->_billingAddress = $billing_address;
        return $this;
    }

    /**
     * Get formatted price value including order currency rate to order website currency
     *
     * @param   float $price
     * @param   bool $addBrackets
     * @return  string
     */
    public function formatPrice($price, $addBrackets = false)
    {
        return number_format($price, 2);
    }

    /**
     * @inheritDoc
     */
    public function getPaymentMethod(): string
    {
        return $this->getData(self::PAYMENT_METHOD);
    }

    /**
     * @inheritDoc
     */
    public function setPaymentMethod(string $payment_method): self
    {
        return $this->setData(self::PAYMENT_METHOD, $payment_method);
    }

    /**
     * @inheritDoc
     */
    public function getBaseDiscountAmount(): float
    {
        return $this->getData(self::BASE_DISCOUNT_AMOUNT);
    }

    /**
     * @inheritDoc
     */
    public function setBaseDiscountAmount($base_discount_amount): self
    {
        return $this->setData(self::BASE_DISCOUNT_AMOUNT, $base_discount_amount);
    }

    /**
     * @inheritDoc
     */
    public function getDiscountAmount(): float
    {
        return $this->getData(self::DISCOUNT_AMOUNT);
    }

    /**
     * @inheritDoc
     */
    public function setDiscountAmount($discount_amount): self
    {
        return $this->setData(self::DISCOUNT_AMOUNT, $discount_amount);
    }

    /**
     * @inheritDoc
     */
    public function getOrderDate(): string
    {
        return $this->getData(self::ORDER_DATE);
    }

    /**
     * @inheritDoc
     */
    public function setOrderDate($order_date): self
    {
        return $this->setData(self::ORDER_DATE, $order_date);
    }

    /**
     * @inheritDoc
     */
    public function getBaseTaxAmount(): float
    {
        return $this->getData(self::BASE_TAX_AMOUNT);
    }

    /**
     * @inheritDoc
     */
    public function setBaseTaxAmount($base_tax_amount): self
    {
        return $this->setData(self::BASE_TAX_AMOUNT, $base_tax_amount);
    }

    /**
     * @inheritDoc
     */
    public function getTaxAmount(): float
    {
        return $this->getData(self::TAX_AMOUNT);
    }

    /**
     * @inheritDoc
     */
    public function setTaxAmount($tax_amount): self
    {
        return $this->setData(self::TAX_AMOUNT, $tax_amount);
    }

    /**
     * @inheritDoc
     */
    public function getBaseShippingAmount(): float
    {
        return $this->getData(self::BASE_SHIPPING_AMOUNT);
    }

    /**
     * @inheritDoc
     */
    public function setBaseShippingAmount($base_shipping_amount): self
    {
        return $this->setData(self::BASE_SHIPPING_AMOUNT, $base_shipping_amount);
    }

    /**
     * @inheritDoc
     */
    public function getShippingAmount(): float
    {
        return $this->getData(self::SHIPPING_AMOUNT);
    }

    /**
     * @inheritDoc
     */
    public function setShippingAmount($shipping_amount): self
    {
        return $this->setData(self::SHIPPING_AMOUNT, $shipping_amount);
    }

    /**
     * @inheritDoc
     */
    public function setItems(array $items): self
    {
        $this->_items = $items;
    }

    /**
     * @inheritDoc
     */
    public function getItems(): array
    {
        if (!$this->_items) {
            $this->_items = $this->itemRepository->getOrderItems($this->getId());
        }

        return $this->_items;
    }

    /**
     * @param \Magento\Sales\Model\Order\Item $item
     * @return $this
     */
    public function addItem(\Dealer4Dealer\SubstituteOrders\Model\Order\Item $item)
    {
        $item->setOrder($this);
        if (!$item->getId()) {
            $this->setItems(array_merge($this->getItems(), [$item]));
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAllItems()
    {
        $items = [];
        foreach ($this->getItems() as $item) {
            if (!$item->isDeleted()) {
                $items[] = $item;
            }
        }

        return $items;
    }

    /**
     * @inheritDoc
     */
    public function getMagentoIncrementId(): int
    {
        return $this->getData(self::MAGENTO_INCREMENT_ID);
    }

    /**
     * @inheritDoc
     */
    public function setMagentoIncrementId($incrementId): self
    {
        return $this->setData(self::MAGENTO_INCREMENT_ID, $incrementId);
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt(): string
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setUpdatedAt($updated): self
    {
        return $this->setData(self::UPDATED_AT, $updated);
    }

    /**
     * @inheritDoc
     */
    public function getAdditionalData(): array
    {
        if ($this->_additionalData == null) {
            $this->_additionalData = [];

            if ($this->getData(self::ADDITIONAL_DATA)) {
                $data = json_decode($this->getData(self::ADDITIONAL_DATA), true);
                foreach ($data as $key => $value) {
                    $this->_additionalData[] = new AdditionalData($key, $value);
                }
            }
        }

        return $this->_additionalData;
    }

    /**
     * @inheritDoc
     */
    public function setAdditionalData($additional_data): self
    {
        $this->_additionalData = $additional_data;
        return $this;
    }

    /**
     * @return bool
     */
    public function isCurrencyDifferent()
    {
        // TODO implement this function correctly
        // For now: return false
        return false;

        return $this->getOrderCurrencyCode() != $this->getBaseCurrencyCode();
    }

    /**
     * Returns shipping_description
     *
     * @return string|null
     */
    public function getShippingDescription()
    {
        return $this->getData('shipping_method');
    }

    /**
     * @inheritDoc
     */
    public function setAttachments(array $fileContent): self
    {
        return $this->setData(self::FILE_CONTENT, $fileContent);
    }

    /**
     * @inheritDoc
     */
    public function getAttachments(): array
    {
        if ($this->_attachments == null) {
            $attachments = $this->attachmentRepository->getAttachmentsByEntityTypeIdentifier(
                $this->getOrderId(),
                $this->getMagentoCustomerId(),
                self::ENTITY
            );

            $files = [];

            foreach ($attachments as $file) {
                $files[] = [
                    'file' => $file->getFile(),
                    'attachment_id' => $file->getAttachmentId()
                ];
            }

            $this->_attachments = $files;
        }

        return $this->_attachments;
    }
}
