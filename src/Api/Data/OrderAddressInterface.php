<?php

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Api\Data;

interface OrderAddressInterface
{
    public const ORDERADDRESS_ID = 'orderaddress_id',
        ORDER_ID = 'order_id',
        PREFIX = 'prefix',
        FIRSTNAME = 'firstname',
        MIDDLENAME = 'middlename',
        LASTNAME = 'lastname',
        SUFFIX = 'suffix',
        COMPANY = 'company',
        STREET = 'street',
        POSTCODE = 'postcode',
        CITY = 'city',
        COUNTRY = 'country',
        PHONE = 'telephone',
        FAX = 'fax',
        ADDITIONAL_DATA = 'additional_data';

    public function getOrderAddressId(): ?int;

    public function setOrderAddressId(int $orderAddressId): self;

    public function getFirstname(): ?string;

    public function setFirstname(string $firstname): self;

    public function getMiddlename(): ?string;

    public function setMiddlename(string $middlename): self;

    public function getLastname(): ?string;

    public function setLastname(string $lastname): self;

    public function getPrefix(): ?string;

    public function setPrefix(string $prefix): self;

    public function getSuffix(): ?string;

    public function setSuffix(string $suffix): self;

    public function getCompany(): ?string;

    public function setCompany(string $company): self;

    public function getStreet(): ?string;

    public function setStreet(string $street): self;

    public function getPostcode(): ?string;

    public function setPostcode(string $postcode): self;

    public function getCity(): ?string;

    public function setCity(string $city): self;

    public function getCountry(): ?string;

    public function setCountry(string $country): self;

    public function getTelephone(): ?string;

    public function setTelephone(string $telephone): self;

    public function getFax(): ?string;

    public function setFax(string $fax): self;

    /**
     * @return AdditionalDataInterface[]
     */
    public function getAdditionalData(): array;

    /**
     * @param AdditionalDataInterface[] $additionalData
     */
    public function setAdditionalData(array $additionalData): self;
}
