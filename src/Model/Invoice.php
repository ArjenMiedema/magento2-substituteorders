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

use Dealer4Dealer\SubstituteOrders\Api\AttachmentRepositoryInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\AdditionalDataInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\OrderAddressInterface;
use Dealer4Dealer\SubstituteOrders\Api\InvoiceItemRepositoryInterface;
use Dealer4Dealer\SubstituteOrders\Api\OrderAddressRepositoryInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\InvoiceInterface;
use Dealer4Dealer\SubstituteOrders\Model\ResourceModel\Invoice as ResourceModel;
use Dealer4Dealer\SubstituteOrders\Model\ResourceModel\Order\CollectionFactory;
use Exception;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Invoice extends AbstractModel implements InvoiceInterface
{
    /**
     * @var string
     */
    public const ENTITY = 'invoice';

    /**
     * @var string
     */
    protected $_eventPrefix = 'substitute_order_invoice';

    /**
     * @var string
     */
    protected $_eventObject = 'invoice';

    protected array $items;
    protected ?OrderAddressInterface $billingAddress;
    protected ?OrderAddressInterface $shippingAddress;
    protected $additionalData;

    protected array $attachments;

    public function __construct(
        Context $context,
        Registry $registry,
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly StoreManagerInterface $storeManager,
        private readonly CollectionFactory $orderCollectionFactory,
        private readonly \Dealer4Dealer\SubstituteOrders\Model\OrderInvoiceRelationFactory $orderInvoiceRelationFactory,
        private readonly InvoiceItemRepositoryInterface $itemRepository,
        private readonly OrderAddressRepositoryInterface $addressRepository,
        private readonly AttachmentRepositoryInterface $attachmentRepository,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    protected function _construct(): void
    {
        $this->_init(ResourceModel::class);
    }

    public function save(): self
    {
        if ($this->additionalData) {
            $data = [];
            foreach ($this->additionalData as $value) {
                $data[$value->getKey()] = $value->getValue();
            }

            $this->setData(self::ADDITIONAL_DATA, json_encode($data));
        }


        if ($this->shippingAddress) {
            if (!$this->getData(self::SHIPPING_ADDRESS_ID) && $this->shippingAddress->getId() != $this->getData(self::SHIPPING_ADDRESS_ID)) {
                try {
                    $oldAddress = $this->addressRepository->getById($this->getData(self::SHIPPING_ADDRESS_ID));
                    $this->shippingAddress->setData(
                        array_merge($oldAddress->getData(), $this->shippingAddress->getData())
                    );
                } catch (Exception $e) { // @codingStandardsIgnoreLine

                }

                $this->shippingAddress->setId($this->getData(self::SHIPPING_ADDRESS_ID));
            }

            $this->shippingAddress->save();
            $this->setData(self::SHIPPING_ADDRESS_ID, $this->shippingAddress->getId());
        }

        if ($this->billingAddress) {
            if (!$this->getData(self::BILLING_ADDRESS_ID) && $this->billingAddress->getId() != $this->getData(self::BILLING_ADDRESS_ID)) {
                try {
                    $oldAddress = $this->addressRepository->getById($this->getData(self::BILLING_ADDRESS_ID));
                    $this->billingAddress->setData(
                        array_merge($oldAddress->getData(), $this->billingAddress->getData())
                    );
                } catch (Exception $e) { // @codingStandardsIgnoreLine

                }

                $this->billingAddress->setId($this->getData(self::BILLING_ADDRESS_ID));
            }

            $this->billingAddress->save();
            $this->setData(self::BILLING_ADDRESS_ID, $this->billingAddress->getId());
        }

        parent::save();

        if ($this->getData(self::ORDER_IDS)) {
            $activeIds = [];
            $collection = $this->orderInvoiceRelationFactory->create()->getCollection()
                ->addFieldToFilter('invoice_id', $this->getId());

            foreach ($collection as $relation) {
                if (in_array($relation->getOrderId(), $this->getData(self::ORDER_IDS))) {
                    $activeIds[$relation->getOrderId()] = true;
                } else {
                    $relation->delete();
                }
            }

            foreach ($this->getData(self::ORDER_IDS) as $orderId) {
                if (!isset($activeIds[$orderId])) {
                    $relation = $this->orderInvoiceRelationFactory->create();
                    $relation->setData(
                        [
                        'order_id' => $orderId,
                        'invoice_id' => $this->getId()
                        ]
                    );
                    $relation->save();
                }
            }
        }

        if ($this->items) {
            $oldItems = $this->itemRepository->getInvoiceItems($this->getId());
            $oldSkus = [];
            $newSkus = [];
            foreach ($oldItems as $item) {
                $oldSkus[$item->getSku()] = $item;
            }

            foreach ($this->items as $item) {
                $oldItem = isset($oldSkus[$item->getSku()]) ? $oldSkus[$item->getSku()] : null;

                if ($oldItem && $oldItem->getInvoiceId() == $this->getId()) {
                    $item->setData(array_merge($oldItem->getData(), $item->getData()));
                    $item->setId($oldItem->getId());
                } else {
                    $item->setId(null);
                }

                $item->setInvoiceId($this->getId());
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

    public function delete(): self
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

    public function getRealOrderId(): int
    {
        $realOrderIdSetting = $this->scopeConfig->getValue(
            'substitute/general/real_order_id',
            ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId()
        );

        return (
            $realOrderIdSetting === 'external'
                ? $this->getData('ext_invoice_id')
                : $this->getData('magento_increment_id')
        ) ?: '-';
    }

    public function getShippingAddress(): ?OrderAddressInterface
    {
        return $this->shippingAddress ??= $this->addressRepository->getById($this->getData(self::SHIPPING_ADDRESS_ID));
    }

    public function setShippingAddress(OrderAddressInterface $shippingAddress): self
    {
        $this->shippingAddress = $shippingAddress;

        return $this;
    }

    public function getBillingAddress(): OrderAddressInterface
    {
        return $this->billingAddress ??= $this->addressRepository->getById($this->getData(self::BILLING_ADDRESS_ID));
    }

    public function setBillingAddress(OrderAddressInterface $billingAddress): self
    {
        $this->billingAddress = $billingAddress;

        return $this;
    }

    public function setItems(array $items): self
    {
        $this->items = $items;

        return $this;
    }

    public function getItems(): array
    {
        return $this->items ??= $this->itemRepository->getInvoiceItems($this->getId());
    }

    /**
     * @return array
     */
    public function getAllItems(): array
    {
        return array_filter(
            $this->getItemsCollection(),
            static fn ($item) => !$item->isDeleted()
        );
    }

    public function getItemsCollection(): array
    {
        return $this->getItems();
    }

    /**
     * Get all orders for invoice
     */
    public function getOrders(): array
    {
        if (!$this->getId()) {
            return [];
        }

        return $this->orderCollectionFactory->create()->filterByInvoice($this);
    }

    public function getInvoiceId(): int
    {
        return (int) $this->getData(self::INVOICE_ID);
    }

    public function setInvoiceId(int $invoiceId): self
    {
        return $this->setData(self::INVOICE_ID, $invoiceId);
    }

    public function getOrderIds(): array
    {
        return $this->getData(self::ORDER_IDS) ?: array_map(
            static fn ($order) => $order->getId(),
            $this->getOrders()
        );
    }

    public function setOrderIds(array $orderIds): self
    {
        return $this->setData(self::ORDER_IDS, array_unique($orderIds));
    }

    public function getMagentoInvoiceId(): int
    {
        return $this->getData(self::MAGENTO_INVOICE_ID);
    }

    public function setMagentoInvoiceId(int $magentoInvoiceId): self
    {
        return $this->setData(self::MAGENTO_INVOICE_ID, $magentoInvoiceId);
    }

    public function getExtInvoiceId(): string
    {
        return $this->getData(self::EXT_INVOICE_ID);
    }

    public function setExtInvoiceId(string $extInvoiceId): self
    {
        return $this->setData(self::EXT_INVOICE_ID, $extInvoiceId);
    }

    public function getPoNumber(): string
    {
        return $this->getData(self::PO_NUMBER);
    }

    public function setPoNumber(string $poNumber): self
    {
        return $this->setData(self::PO_NUMBER, $poNumber);
    }

    public function getMagentoCustomerId(): int
    {
        return $this->getData(self::MAGENTO_CUSTOMER_ID);
    }

    public function setMagentoCustomerId(int $magentoCustomerId): self
    {
        return $this->setData(self::MAGENTO_CUSTOMER_ID, $magentoCustomerId);
    }

    public function getBaseTaxAmount(): float
    {
        return $this->getData(self::BASE_TAX_AMOUNT);
    }

    public function setBaseTaxAmount(float $baseTaxAmount): self
    {
        return $this->setData(self::BASE_TAX_AMOUNT, $baseTaxAmount);
    }

    public function getBaseDiscountAmount(): float
    {
        return $this->getData(self::BASE_DISCOUNT_AMOUNT);
    }

    public function setBaseDiscountAmount(float $baseDiscountAmount): self
    {
        return $this->setData(self::BASE_DISCOUNT_AMOUNT, $baseDiscountAmount);
    }

    public function getBaseShippingAmount(): float
    {
        return $this->getData(self::BASE_SHIPPING_AMOUNT);
    }

    public function setBaseShippingAmount(float $baseShippingAmount): self
    {
        return $this->setData(self::BASE_SHIPPING_AMOUNT, $baseShippingAmount);
    }

    public function getBaseSubtotal(): float
    {
        return $this->getData(self::BASE_SUBTOTAL);
    }

    public function setBaseSubtotal(float $baseSubtotal): self
    {
        return $this->setData(self::BASE_SUBTOTAL, $baseSubtotal);
    }

    public function getBaseGrandTotal(): float
    {
        return $this->getData(self::BASE_GRANDTOTAL);
    }

    public function setBaseGrandTotal(float $baseGrandTotal): self
    {
        return $this->setData(self::BASE_GRANDTOTAL, $baseGrandTotal);
    }

    public function getTaxAmount(): float
    {
        return $this->getData(self::TAX_AMOUNT);
    }

    public function setTaxAmount(float $taxAmount): self
    {
        return $this->setData(self::TAX_AMOUNT, $taxAmount);
    }

    public function getDiscountAmount(): float
    {
        return $this->getData(self::DISCOUNT_AMOUNT);
    }

    public function setDiscountAmount(float $discountAmount): self
    {
        return $this->setData(self::DISCOUNT_AMOUNT, $discountAmount);
    }

    public function getShippingAmount(): float
    {
        return $this->getData(self::SHIPPING_AMOUNT);
    }

    public function setShippingAmount(float $shippingAmount): self
    {
        return $this->setData(self::SHIPPING_AMOUNT, $shippingAmount);
    }

    public function getSubtotal(): float
    {
        return $this->getData(self::SUBTOTAL);
    }

    public function setSubtotal(float $subtotal): self
    {
        return $this->setData(self::SUBTOTAL, $subtotal);
    }

    public function getGrandtotal(): float
    {
        return $this->getData(self::GRANDTOTAL);
    }

    public function setGrandtotal(float $grandTotal): self
    {
        return $this->setData(self::GRANDTOTAL, $grandTotal);
    }

    public function getInvoiceDate(): string
    {
        return $this->getData(self::INVOICE_DATE);
    }

    public function setInvoiceDate(string $invoiceDate): self
    {
        return $this->setData(self::INVOICE_DATE, $invoiceDate);
    }

    public function getState(): string
    {
        return $this->getData(self::STATE);
    }

    public function setState(string $state): self
    {
        return $this->setData(self::STATE, $state);
    }

    public function getMagentoIncrementId(): string
    {
        return $this->getData(self::MAGENTO_INCREMENT_ID);
    }

    public function setMagentoIncrementId(string $magentoIncrementId): self
    {
        return $this->setData(self::MAGENTO_INCREMENT_ID, $magentoIncrementId);
    }

    public function getUpdatedAt(): string
    {
        return $this->getData(self::UPDATED_AT);
    }

    public function setUpdatedAt(string $updated): self
    {
        return $this->setData(self::UPDATED_AT, $updated);
    }

    public function getAdditionalData(): array
    {
        if ($this->additionalData === null) {
            $this->additionalData = [];

            if ($this->getData(self::ADDITIONAL_DATA)) {
                $data = json_decode($this->getData(self::ADDITIONAL_DATA), true);

                foreach ($data as $key => $value) {
                    $this->additionalData[] = new AdditionalData($key, $value);
                }
            }
        }

        return $this->additionalData;
    }

    public function setAdditionalData(array $additionalData): self
    {
        $this->additionalData = $additionalData;

        return $this;
    }

    public function setAttachments(array $attachments): self
    {
        return $this->setData(self::FILE_CONTENT, $attachments);
    }

    public function getAttachments(): array
    {
        if ($this->attachments === null) {
            $attachments = $this->attachmentRepository->getAttachmentsByEntityTypeIdentifier(
                $this->getInvoiceId(),
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

            $this->attachments = $files;
        }

        return $this->attachments;
    }
}
