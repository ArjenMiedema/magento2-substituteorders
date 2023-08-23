<?php

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Console\Command;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Address\AbstractAddress;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Model\Quote\AddressFactory;
use Magento\Quote\Model\Cart\Data\CartItemFactory;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteFactory;
use Magento\Quote\Model\QuoteManagement;
use Magento\Quote\Model\ResourceModel\Quote as QuoteResourceModel;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateSubstituteOrders extends Command
{
    private const COMMAND_NAME = 'dealer4dealer:substitute-order:migrate',
        COMMAND_DESCRIPTION    = 'Migrate all substitute orders that do not exist in Magento yet to actual Magento orders';

    public function __construct(
        private readonly QuoteFactory $quoteFactory,
        private readonly StoreManagerInterface $storeManager,
        private readonly AddressFactory $quoteAddressFactory,
        private readonly QuoteResourceModel $quoteResourceModel,
        private readonly QuoteManagement $cartManagement,
        private readonly CartItemFactory $cartItemFactory,
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly CustomerRepositoryInterface $customerRepository,
        string $name = null
    ) {
        parent::__construct($name ?? self::COMMAND_NAME);
    }

    protected function configure(): void
    {
        $this->setDescription(self::COMMAND_DESCRIPTION);
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        foreach ($this->fetchAllSubstituteOrders() as $orderData) {
            if ($this->hasMagentoOrder($orderData)) {
                continue;
            }

            try {
                $customer = $this->customerRepository
                    ->getById($orderData['magento_customer_id']);
            } catch (NoSuchEntityException|LocalizedException) {
                $customer = null;
            }

            $customerData = $this->getAddressDataFromOrder($orderData, AbstractAddress::TYPE_BILLING);
            $quote        = $this->quoteFactory->create();
            $quote->setStore($this->getStoreFromCustomer($customer));

            if ($customer instanceof CustomerInterface) {
                $quote->assignCustomer($customer);
            }

            $this->addProductsToQuote($orderData['items'], $quote);

            $quote->setBillingAddress(
                $this->createQuoteAddress($orderData, AbstractAddress::TYPE_BILLING)
            );

            $quote->setShippingAddress(
                $this->createQuoteAddress($orderData, AbstractAddress::TYPE_SHIPPING)
            );

            $shippingAddress = $quote->getShippingAddress();
            $shippingAddress->setCollectShippingRates(true)
                ->collectShippingRates()
                ->setShippingMethod($orderData['shipping_method']);

            $quote->setBaseGrandTotal($orderData['base_grandtotal'])
                ->setBaseSubtotal($orderData['base_subtotal'])
                ->setGrandTotal($orderData['grandtotal'])
                ->setSubtotal($orderData['subtotal'])
                ->setCreatedAt($orderData['order_date'])
                ->setUpdatedAt($orderData['updated_at'])
                ->setCustomerEmail($customer?->getEmail())
                ->setCustomerIsGuest($customer instanceof CustomerInterface)
                ->setCustomerGroupId($customer?->getGroupId())
                ->setCustomerFirstname($customerData['firstname'])
                ->setCustomerLastname($customerData['lastname'])
                ->setCustomerMiddlename($customerData['middlename'])
                ->setCustomerPrefix($customerData['prefix'])
                ->setCustomerSuffix($customerData['suffix']);

            $quote->getPayment()->importData(['method' => 'checkmo']);
            $quote->collectTotals();
            $this->quoteResourceModel->save($quote);

            $this->cartManagement->submit(
                $quote,
                [
                    'increment_id' => $orderData['magento_increment_id'],
                    'created_at' => $orderData['order_date'],
                    'ext_order_id' => $orderData['ext_order_id'],
                    'external_customer_id' => $orderData['external_customer_id'],
                    'state' => $orderData['state'],
                    'base_discount_amount' => $orderData['base_discount_amount'],
                    'discount_amount' => $orderData['discount_amount'],
                    'base_tax_amount' => $orderData['base_tax_amount'],
                    'tax_amount' => $orderData['tax_amount'],
                    'base_shipping_amount' => $orderData['base_shipping_amount'],
                    'shipping_amount' => $orderData['shipping_amount']
                ]
            );

            exit;
        }
    }

    private function fetchAllSubstituteOrders(): array
    {
        $connection = $this->quoteResourceModel->getConnection();
        $query      = $connection->select()
            ->from($this->quoteResourceModel->getTable('dealer4dealer_order'));

        return $connection->fetchAll($query);
    }

    private function hasMagentoOrder(array $substituteOrder): bool
    {
        if (!$substituteOrder['magento_increment_id']) {
            return false;
        }

        $collection = $this->orderRepository
            ->getList(
                $this->searchCriteriaBuilder
                    ->addFilter(
                        OrderInterface::INCREMENT_ID,
                        $substituteOrder['magento_increment_id']
                    )
                    ->create()
            )
            ->getItems();

        return count($collection) > 0;

    }

    private function createQuoteAddress(
        array $orderData,
        string $addressType
    ): AddressInterface {
        $addressData = $this->getAddressDataFromOrder($orderData, $addressType);

        /** @var AddressInterface $orderAddress */
        $orderAddress = $this->quoteAddressFactory->create();
        $orderAddress->setAddressType($addressType);
        $orderAddress->setFirstname($addressData['firstname'])
            ->setLastname($addressData['lastname'])
            ->setMiddlename($addressData['middlename'])
            ->setPrefix($addressData['prefix'])
            ->setSuffix($addressData['suffix'])
            ->setCompany($addressData['company'])
            ->setStreet($addressData['street'])
            ->setPostcode($addressData['postcode'])
            ->setCity($addressData['city'])
            ->setCountryId($addressData['country'])
            ->setTelephone($addressData['telephone'])
            ->setFax($addressData['fax']);

        return $orderAddress;
    }

    private function addProductsToQuote(
        array $items,
        Quote $quote
    ): void {
        foreach ($items as $item) {
            /** @var CartItemInterface $quoteItem */
            $quoteItem = $this->cartItemFactory->create();
            $quoteItem->setName($item['name'])
                ->setSku($item['sku'])
                ->setBasePrice($item['base_price'])
                ->setPrice($item['price'])
                ->setBaseRowTotal($item['base_row_total'])
                ->setRowTotal($item['row_total'])
                ->setBaseTaxAmount($item['base_tax_amount'])
                ->setTaxAmount($item['tax_amount'])
                ->setQty($item['qty'])
                ->setBaseDiscountAmount($item['base_discount_amount'])
                ->setDiscountAmount($item['discount_amount']);

            $quote->addItem($quoteItem);
        }
    }

    private function getStoreFromCustomer(?CustomerInterface $customer): StoreInterface
    {
        return $customer instanceof CustomerInterface
            ? $this->storeManager->getStore($customer->getStoreId())
            : $this->storeManager->getDefaultStoreView();
    }

    private function getAddressDataFromOrder(
        array $orderData,
        string $addressType
    ): array {
        $connection = $this->quoteResourceModel->getConnection();
        $query      = $connection->select()
            ->from($this->quoteResourceModel->getTable('dealer4dealer_orderaddress'))
            ->where(
                'orderaddress_id = ?',
                $addressType === AbstractAddress::TYPE_SHIPPING
                    ? $orderData['shipping_address_id']
                    : $orderData['billing_address_id']
            );

        return $connection->fetchRow($query);
    }
}
