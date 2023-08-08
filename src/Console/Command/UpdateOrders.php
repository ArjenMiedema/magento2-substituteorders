<?php

namespace Dealer4Dealer\SubstituteOrders\Console\Command;

use Dealer4Dealer\SubstituteOrders\Observer\Sales\OrderInvoiceSaveAfter;
use Dealer4Dealer\SubstituteOrders\Observer\Sales\OrderSaveAfter;
use Dealer4Dealer\SubstituteOrders\Observer\Sales\OrderShipmentSaveAfter;
use Exception;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\ObjectManagerInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;
use Magento\Sitemap\Model\Observer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateOrders extends Command
{
    private const COMMAND_NAME = 'substituteorders:updateorders',
        COMMAND_DESCRIPTION    = 'Copy existing order information to substitute orders';

    /**@var OrderSaveAfter */
    protected $orderSaveAfter;

    /** @var \Dealer4Dealer\SubstituteOrders\Observer\Sales\OrderInvoiceSaveAfter */
    protected $invoiceSaveAfter;

    /** @var \Dealer4Dealer\SubstituteOrders\Observer\Sales\OrderShipmentSaveAfter */
    protected $shipmentSaveAfter;

    /**@var ObjectManagerInterface */
    protected $objectManager;

    /**@var OrderFactory */
    protected $orderFactory;

    public function __construct(
        OrderSaveAfter $orderSaveAfter,
        \Dealer4Dealer\SubstituteOrders\Observer\Sales\OrderInvoiceSaveAfter $orderInvoiceSaveAfter,
        \Dealer4Dealer\SubstituteOrders\Observer\Sales\OrderShipmentSaveAfter $shipmentSaveAfter,
        OrderFactory $orderFactory,
        private readonly State $state,
        $name = null
    ) {
        parent::__construct($name);

        $this->orderSaveAfter = $orderSaveAfter;
        $this->invoiceSaveAfter = $orderInvoiceSaveAfter;
        $this->shipmentSaveAfter = $shipmentSaveAfter;
        $this->orderFactory = $orderFactory;
    }

    protected function configure(): void
    {
        $this->setName(self::COMMAND_NAME)
            ->setDescription(self::COMMAND_DESCRIPTION);

        parent::configure();
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $this->state->setAreaCode(Area::AREA_FRONTEND);

        $collection = $this->orderFactory->create()->getCollection();
        $size = $collection->getSize();
        $maxPages = ceil($size / 250);
        $page = 1;

        while (($page - 1) * 250 < $size) {
            print("Processing page {$page} of {$maxPages}\n");

            $collection->clear()->setPageSize(250)->setCurPage($page)->load();
            /** @var Order $order */
            foreach ($collection as $order) {
                // Copy the order

                /** @var \Magento\Framework\Event\Observer $observer */
                $observer = $this->objectManager->get('\Magento\Framework\Event\Observer');
                $observer->setOrder($order);

                try {
                    $this->orderSaveAfter->execute($observer);
                } catch (Exception $e) {
                    print("ERROR: Could not update order {$order->getIncrementId()}: {$e->getMessage()}\n");
                }

                // Copy the invoices
                foreach ($order->getInvoiceCollection() as $invoice) {
                    /** @var \Magento\Framework\Event\Observer */
                    $observer1 = $this->objectManager->get('\Magento\Framework\Event\Observer');
                    $observer1->setInvoice($invoice);

                    try {
                        $this->invoiceSaveAfter->execute($observer1);
                    } catch (Exception $e) {
                        print("ERROR: Could not update invoice: {$e->getMessage()}\n");
                    }
                }

                foreach ($order->getShipmentsCollection() as $shipment) {
                    /** @var \Magento\Framework\Event\Observer */
                    $observer = $this->objectManager->get('\Magento\Framework\Event\Observer');
                    $observer->setShipment($shipment);

                    try {
                        $this->shipmentSaveAfter->execute($observer);
                    } catch (Exception $e) {
                        print ("ERROR: Could not update shipment: {$e->getMessage()}\n");
                    }
                }
            }

            $page++;
        }
    }
}
