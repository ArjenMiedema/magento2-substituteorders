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

namespace Dealer4Dealer\SubstituteOrders\src\Model\Order\Address;

use Magento\Sales\Model\Order\Address;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Directory\Model\CountryFactory;
use function Dealer4Dealer\SubstituteOrders\Model\Order\Address\__;

/**
 * Class Validator
 */
class Validator
{
    /**
     * @var array
     */
    protected $required = [
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

    /**
     * @var DirectoryHelper
     */
    protected $directoryHelper;

    /**
     * @var CountryFactory
     */
    protected $countryFactory;

    /**
     * @param DirectoryHelper $directoryHelper
     * @param CountryFactory $countryFactory
     */
    public function __construct(
        DirectoryHelper $directoryHelper,
        CountryFactory $countryFactory
    ) {
        $this->directoryHelper = $directoryHelper;
        $this->countryFactory = $countryFactory;
    }

    /**
     *
     * @param \Magento\Sales\Model\Order\Address $address
     * @return array
     */
    public function validate(\Dealer4Dealer\SubstituteOrders\src\Api\Data\OrderAddressInterface $address)
    {
        $warnings = [];
        foreach ($this->required as $code => $label) {
            if (!$address->hasData($code)) {
                $warnings[] = sprintf('%s is a required field', $label);
            }
        }
        if (!filter_var($address->getEmail(), FILTER_VALIDATE_EMAIL)) {
            $warnings[] = 'Email has a wrong format';
        }
        if (!filter_var(in_array($address->getAddressType(), [Address::TYPE_BILLING, Address::TYPE_SHIPPING]))) {
            $warnings[] = 'Address type doesn\'t match required options';
        }
        return $warnings;
    }

    /**
     * Validate address attribute for customer creation
     *
     * @return bool|array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     *
     * @param Address $address
     */
    public function validateForCustomer(\Dealer4Dealer\SubstituteOrders\src\Api\Data\OrderAddressInterface $address)
    {
        if ($address->getShouldIgnoreValidation()) {
            return true;
        }

        $errors = [];

        if ($this->isEmpty($address->getFirstname())) {
            $errors[] = __('Please enter the first name.');
        }
        if ($this->isEmpty($address->getLastname())) {
            $errors[] = __('Please enter the last name.');
        }
        if ($this->isEmpty($address->getStreetLine(1))) {
            $errors[] = __('Please enter the street.');
        }
        if ($this->isEmpty($address->getCity())) {
            $errors[] = __('Please enter the city.');
        }
        if ($this->isEmpty($address->getTelephone())) {
            $errors[] = __('Please enter the phone number.');
        }

        $countryId = $address->getCountryId();

        if ($this->isZipRequired($countryId) && $this->isEmpty($address->getPostcode())) {
            $errors[] = __('Please enter the zip/postal code.');
        }
        if ($this->isEmpty($countryId)) {
            $errors[] = __('Please enter the country.');
        }
        if ($this->isStateRequired($countryId) && $this->isEmpty($address->getRegionId())) {
            $errors[] = __('Please enter the state/province.');
        }

        return empty($errors) ? true : $errors;
    }

    /**
     * Check if value is empty
     *
     * @param mixed $value
     * @return bool
     */
    protected function isEmpty($value)
    {
        return empty($value);
    }

    /**
     * Checks if zip for current country id is required
     *
     * @param string $countryId
     * @return bool
     */
    protected function isZipRequired($countryId)
    {
        return !in_array($countryId, $this->directoryHelper->getCountriesWithOptionalZip());
    }

    /**
     * Checks if state for current country id is required
     *
     * @param string $countryId
     * @return bool
     */
    protected function isStateRequired($countryId)
    {
        $country = $this->countryFactory->create()->load($countryId);
        return $this->directoryHelper->isRegionRequired($countryId) && $country->getRegionCollection()->getSize();
    }
}
