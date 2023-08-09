<?php

declare(strict_types=1);

// phpcs:disable GlobalCommon.NamingConventions.ValidVariableName.IllegalVariableNameUnderscore
// phpcs:disable PSR2.Classes.PropertyDeclaration.Underscore
// phpcs:disable PSR2.Methods.MethodDeclaration.Underscore

namespace Dealer4Dealer\SubstituteOrders\Model;

use Dealer4Dealer\SubstituteOrders\Api\AttachmentRepositoryInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\OrderAddressInterface;
use Dealer4Dealer\SubstituteOrders\Api\Data\ShipmentInterface;
use Dealer4Dealer\SubstituteOrders\Api\OrderAddressRepositoryInterface;
use Dealer4Dealer\SubstituteOrders\Api\ShipmentItemRepositoryInterface;
use Dealer4Dealer\SubstituteOrders\Model\AdditionalData;
use Dealer4Dealer\SubstituteOrders\Model\ResourceModel\Shipment as ResourceModel;
use Dealer4Dealer\SubstituteOrders\Model\ShipmentTracking;
use Exception;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Shipment extends AbstractModel implements ShipmentInterface
{
    /**
     * @var string
     */
    public const ENTITY = 'shipment';

    /**
     * @var string
     */
    protected $_eventPrefix = 'substitute_order_shipment';

    /**
     * @var string
     */
    protected $_eventObject = 'shipment';

    protected ?array $items = null;

    protected ?OrderAddressInterface $billingAddress = null;

    protected ?OrderAddressInterface $shippingAddress = null;

    protected ?array $tracking = null;

    protected ?array $additionalData = null;

    protected ?array $attachments = null;

    public function __construct(
        Context $context,
        Registry $registry,
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly StoreManagerInterface $storeManager,
        private readonly ShipmentItemRepositoryInterface $itemRepository,
        private readonly \Dealer4Dealer\SubstituteOrders\Api\OrderAddressRepositoryInterface $addressRepository,
        private readonly AttachmentRepositoryInterface $attachmentRepository,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    protected function _construct(): void
    {
        $this->_init(ResourceModel::class);
    }

    public function save(): self
    {
        if ($this->tracking) {
            $trackingData = [];
            foreach ($this->tracking as $tracker) {
                $trackingData[] = $tracker->getArray();
            }

            $this->setData(self::TRACKING, json_encode($trackingData));
        }

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

        if ($this->items) {
            $oldItems = $this->itemRepository->getShipmentItems($this->getId());
            $oldSkus = [];
            $newSkus = [];
            foreach ($oldItems as $item) {
                $oldSkus[$item->getSku()] = $item;
            }

            foreach ($this->items as $item) {
                $oldItem = isset($oldSkus[$item->getSku()]) ? $oldSkus[$item->getSku()] : null;

                if ($oldItem && $oldItem->getShipmentId() == $this->getId()) {
                    $item->setData(array_merge($oldItem->getData(), $item->getData()));
                    $item->setId($oldItem->getId());
                } else {
                    $item->setId(null);
                }

                $item->setShipmentId($this->getId());
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

    public function getRealOrderId(): string
    {
        $realOrderIdSetting = $this->scopeConfig->getValue(
            'substitute/general/real_order_id',
            ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId()
        );

        if ($realOrderIdSetting == 'external') {
            $orderId = $this->getData('ext_order_id');
        } else {
            $orderId = $this->getData('magento_increment_id');
        }

        return $orderId ? $orderId : '-';
    }

    public function getShippingAddress(): OrderAddressInterface
    {
        if (!$this->shippingAddress) {
            try {
                $this->shippingAddress = $this->addressRepository->getById($this->getData(self::SHIPPING_ADDRESS_ID));
            } catch (Exception) {
            }
        }

        return $this->shippingAddress;
    }

    public function setShippingAddress(OrderAddressInterface $shippingAddress): self
    {
        $this->shippingAddress = $shippingAddress;
        return $this;
    }

    public function getBillingAddress(): OrderAddressInterface
    {
        if (!$this->billingAddress) {
            try {
                $this->billingAddress = $this->addressRepository->getById($this->getData(self::BILLING_ADDRESS_ID));
            } catch (Exception $e) { // @codingStandardsIgnoreLine

            }
        }

        return $this->billingAddress;
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
        if (!$this->items) {
            $this->items = $this->itemRepository->getShipmentItems($this->getId());
        }

        return $this->items;
    }

    public function getTracking(): array
    {
        if ($this->tracking == null) {
            $this->tracking = [];

            if ($this->getData(self::TRACKING)) {
                $tracking = json_decode($this->getData(self::TRACKING), true);
                foreach ($tracking as $track) {
                    $this->tracking[] = ShipmentTracking::createByArray($track);
                }
            }
        }

        return $this->tracking;
    }

    public function setTracking(string|array $tracking): self
    {
        if (is_string($tracking)) {
            $tracking = json_decode($tracking);
        }

        $this->tracking = $tracking;

        return $this;
    }

    public function getAdditionalData(): array
    {
        if ($this->additionalData == null) {
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

    public function getShipmentId(): int
    {
        return $this->getData(self::SHIPMENT_ID);
    }

    public function setShipmentId(int $shipmentId): self
    {
        return $this->setData(self::SHIPMENT_ID, $shipmentId);
    }

    public function getExtShipmentId(): string
    {
        return $this->getData(self::EXT_SHIPMENT_ID);
    }

    public function setExtShipmentId(string $extShipmentId): self
    {
        return $this->setData(self::EXT_SHIPMENT_ID, $extShipmentId);
    }

    public function getCustomerId(): int
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    public function setCustomerId(int $customerId): self
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    public function getOrderId(): int
    {
        return $this->getData(self::ORDER_ID);
    }

    public function setOrderId(int $orderId): self
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    public function getInvoiceId(): int
    {
        return $this->getData(self::INVOICE_ID);
    }

    public function setInvoiceId(int $invoiceId): self
    {
        return $this->setData(self::INVOICE_ID, $invoiceId);
    }

    public function getShipmentStatus(): string
    {
        return $this->getData(self::SHIPMENT_STATUS);
    }

    public function setShipmentStatus(string $shipmentStatus): self
    {
        return $this->setData(self::SHIPMENT_STATUS, $shipmentStatus);
    }

    public function getIncrementId(): int
    {
        return $this->getData(self::INCREMENT_ID);
    }

    public function setIncrementId(int $incrementId): self
    {
        return $this->setData(self::INCREMENT_ID, $incrementId);
    }

    public function getCreatedAt(): string
    {
        return $this->getData(self::CREATED_AT);
    }

    public function setCreatedAt(string $createdAt): self
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    public function getUpdatedAt(): string
    {
        return $this->getData(self::UPDATED_AT);
    }

    public function setUpdatedAt(string $updatedAt): self
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    public function getName(): string
    {
        return $this->getData(self::NAME);
    }

    public function setName(string $name): self
    {
        return $this->setData(self::NAME, $name);
    }

    public function setAttachments(array $attachments): self
    {
        return $this->setData(self::FILE_CONTENT, $attachments);
    }

    public function getAttachments(): array
    {
        if ($this->attachments == null) {
            $attachments = $this->attachmentRepository
                ->getAttachmentsByEntityTypeIdentifier(
                    $this->getShipmentId(),
                    $this->getCustomerId(),
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

    public function getShippingMethod(): string
    {
        return $this->getData(self::SHIPPING_METHOD);
    }

    public function setShippingMethod(string $shippingMethod): self
    {
        return $this->setData(self::SHIPPING_METHOD, $shippingMethod);
    }
}
