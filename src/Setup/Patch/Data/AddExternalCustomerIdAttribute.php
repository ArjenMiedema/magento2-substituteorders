<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Setup\Patch\Data;

use Magento\Customer\Model\Customer;
use Magento\Customer\Model\ResourceModel\Attribute;
use Magento\Eav\Model\Config;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;

class AddExternalCustomerIdAttribute implements DataPatchInterface, PatchRevertableInterface
{
    public function __construct(
        private EavSetupFactory $eavSetupFactory,
        private Config $eavConfig,
        private Attribute $attributeResource
    ) {
    }

    public const ATTRIBUTE_CODE_EXTERNAL_CUSTOMER_ID = 'external_customer_id';

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }

    public function apply(): self
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create();

        $eavSetup->addAttribute(
            Customer::ENTITY,
            self::ATTRIBUTE_CODE_EXTERNAL_CUSTOMER_ID,
            [
                'type' => 'varchar',
                'label' => 'External Customer Id',
                'input' => 'text',
                'required' => false,
                'visible' => true,
                'position' => 999,
                'system' => 0
            ]
        );

        $attributeSetId   = $eavSetup->getDefaultAttributeSetId(Customer::ENTITY);
        $attributeGroupId = $eavSetup->getDefaultAttributeGroupId(Customer::ENTITY);

        $attribute = $this->eavConfig->getAttribute(
            Customer::ENTITY,
            self::ATTRIBUTE_CODE_EXTERNAL_CUSTOMER_ID
        );
        $attribute->setData('attribute_set_id', $attributeSetId);
        $attribute->setData('attribute_group_id', $attributeGroupId);

        $attribute->setData(
            'used_in_forms',
            ['adminhtml_customer']
        );

        $this->attributeResource->save($attribute);

        return $this;
    }

    public function revert(): void
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create();
        $eavSetup->removeAttribute(
            Customer::ENTITY,
            self::ATTRIBUTE_CODE_EXTERNAL_CUSTOMER_ID
        );
    }
}
