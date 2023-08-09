<?php

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Model\Order\Address;

use Dealer4Dealer\SubstituteOrders\Api\Data\OrderAddressInterface;
use Magento\Customer\Model\Address\Config as AddressConfig;
use Magento\Framework\Event\ManagerInterface as EventManager;

class Renderer
{
    public function __construct(
        private readonly AddressConfig $addressConfig,
        private readonly EventManager $eventManager
    ) {
    }

    public function format(
        OrderAddressInterface $address,
        string $type
    ): ?string {
        /* Fix to show address */
        $address->setData('country_id', $address->getCountry());

        $formatType = $this->addressConfig->getFormatByCode($type);

        if (!$formatType || !$formatType->getRenderer()) {
            return null;
        }

        $this->eventManager->dispatch(
            'customer_address_format',
            ['type' => $formatType, 'address' => $address]
        );

        return $formatType->getRenderer()->renderArray($address->getData());
    }
}
