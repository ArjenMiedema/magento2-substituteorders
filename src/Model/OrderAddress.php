<?php

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Model;

use Dealer4Dealer\SubstituteOrders\Api\Data\OrderAddressInterface;
use Dealer4Dealer\SubstituteOrders\Model\AdditionalData;
use Dealer4Dealer\SubstituteOrders\Model\ResourceModel\OrderAddress as ResourceModel;
use Magento\Framework\Model\AbstractModel;

class OrderAddress extends AbstractModel implements OrderAddressInterface
{
    public const ENTITY = 'order_address';

    /**
     * @var string
     */
    protected $_eventPrefix = 'substitute_order_order_adress';

    /**
     * @var string
     */
    protected $_eventObject = 'address';

    protected ?array $additionalData = null;

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

        return parent::save();
    }

    public function getName(): string
    {
        $name = '';
        if ($this->getPrefix()) {
            $name .= $this->getPrefix() . ' ';
        }

        $name .= $this->getFirstname();
        if ($this->getMiddlename()) {
            $name .= ' ' . $this->getMiddlename();
        }

        $name .= ' ' . $this->getLastname();
        if ($this->getSuffix()) {
            $name .= ' ' . $this->getSuffix();
        }

        return $name;
    }

    public function getOrderAddressId(): int
    {
        return $this->getData(self::ORDERADDRESS_ID);
    }

    public function setOrderAddressId(int $orderAddressId): self
    {
        return $this->setData(self::ORDERADDRESS_ID, $orderAddressId);
    }

    public function getFirstname(): string
    {
        return $this->getData(self::FIRSTNAME);
    }

    public function setFirstname(string $firstname): self
    {
        return $this->setData(self::FIRSTNAME, $firstname);
    }

    public function getMiddlename(): string
    {
        return $this->getData(self::MIDDLENAME);
    }

    public function setMiddlename(string $middlename): self
    {
        return $this->setData(self::MIDDLENAME, $middlename);
    }

    public function getLastname(): string
    {
        return $this->getData(self::LASTNAME);
    }

    public function setLastname(string $lastname): self
    {
        return $this->setData(self::LASTNAME, $lastname);
    }

    public function getSuffix(): string
    {
        return $this->getData(self::SUFFIX);
    }

    public function setSuffix(string $suffix): self
    {
        return $this->setData(self::SUFFIX, $suffix);
    }

    public function getPrefix(): string
    {
        return $this->getData(self::PREFIX);
    }

    public function setPrefix(string $prefix): self
    {
        return $this->setData(self::PREFIX, $prefix);
    }

    public function getCompany(): string
    {
        return $this->getData(self::COMPANY);
    }

    public function setCompany(string $company): self
    {
        return $this->setData(self::COMPANY, $company);
    }

    public function getStreet(): string
    {
        return $this->getData(self::STREET);
    }

    public function setStreet(string $street): self
    {
        return $this->setData(self::STREET, $street);
    }

    public function getPostcode(): string
    {
        return $this->getData(self::POSTCODE);
    }

    public function setPostcode(string $postcode): self
    {
        return $this->setData(self::POSTCODE, $postcode);
    }

    public function getCity(): string
    {
        return $this->getData(self::CITY);
    }

    public function setCity(string $city): self
    {
        return $this->setData(self::CITY, $city);
    }

    public function getCountry(): string
    {
        return $this->getData(self::COUNTRY);
    }

    public function setCountry(string $country): self
    {
        return $this->setData(self::COUNTRY, $country);
    }

    public function getTelephone(): string
    {
        return $this->getData(self::PHONE);
    }

    public function setTelephone(string $phone): self
    {
        return $this->setData(self::PHONE, $phone);
    }

    public function getFax(): string
    {
        return $this->getData(self::FAX);
    }

    public function setFax(string $fax): self
    {
        return $this->setData(self::FAX, $fax);
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
}
