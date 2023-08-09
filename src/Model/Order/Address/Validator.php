<?php

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Model\Order\Address;

use Magento\Sales\Api\Data\OrderAddressInterface;

class Validator
{
    protected array $required = [
        'parent_id' => 'Parent Order Id',
        'postcode' => 'Zip code',
        'lastname' => 'Last name',
        'street' => 'Street',
        'city' => 'City',
        'email' => 'Email',
        'telephone' => 'Phone Number',
        'country_id' => 'Country',
        'firstname' => 'First Name',
        'address_type' => 'Address Type',
    ];

    public function validate(OrderAddressInterface $address): array
    {
        $warnings = [];

        foreach ($this->required as $code => $label) {
            if (!$address->hasData($code)) {
                $warnings[] = __('%1 is a required field', $label);
            }
        }

        if (!filter_var($address->getEmail(), FILTER_VALIDATE_EMAIL)) {
            $warnings[] = __('Email has a wrong format');
        }

        if (
            !filter_var(
                in_array(
                    $address->getAddressType(),
                    [Address::TYPE_BILLING, Address::TYPE_SHIPPING]
                )
            )
        ) {
            $warnings[] = __('Address type doesn\'t match required options');
        }

        return $warnings;
    }
}
