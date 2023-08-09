<?php

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Observer\Sales;

use Dealer4Dealer\SubstituteOrders\Api\ShipmentRepositoryInterface;
use Dealer4Dealer\SubstituteOrders\Model\ShipmentTracking;
use Exception;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order\Shipment\Track;

class OrderShipmentTrackSaveAfter implements ObserverInterface
{
    public function __construct(
        private readonly ShipmentRepositoryInterface $shipmentRepository
    ) {
    }

    public function execute(Observer $observer): void
    {
        /** @var Track $track */
        $track = $observer->getData('track');

        try {
            $shipment = $track->getShipment();
        } catch (Exception) {
            return;
        }

        try {
            $subShipment = $this->shipmentRepository->getByIncrementId((int) $shipment->getIncrementId());
        } catch (LocalizedException) {
            return;
        }

        # update all trackers
        $trackers = [];
        foreach ($shipment->getTracks() as $track) {
            $trackers[] = new ShipmentTracking(
                $track->getTitle(),
                $track->getTrackNumber()
            );
        }

        $subShipment->setTracking($trackers);
        $subShipment->save();
    }
}
