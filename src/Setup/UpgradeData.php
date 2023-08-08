<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 11-10-2018
 * Time: 09:17
 */

namespace Dealer4Dealer\SubstituteOrders\src\Setup;

use Magento\Customer\Setup\CustomerSetup;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Entity\Attribute\Set as AttributeSet;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{
    /** @var CustomerSetupFactory */
    protected $customerSetupFactory;

    /** @var AttributeSetFactory */
    protected $attributeSetFactory;

    /**
     * InstallData constructor.
     *
     * @param CustomerSetupFactory $customerSetupFactory
     * @param AttributeSetFactory $attributeSetFactory
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        AttributeSetFactory $attributeSetFactory
    ) {
        $this->attributeSetFactory = $attributeSetFactory;
        $this->customerSetupFactory = $customerSetupFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
        $installer = $setup;
        $installer->startSetup();

        if (version_compare($context->getVersion(), '2.0.2', "<")){
            $this->createExternalIdAttribute($installer);
        }

        $installer->endSetup();
    }

    private function createExternalIdAttribute(ModuleDataSetupInterface $setup) {
        $attributeCode = 'external_customer_id';

        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup'=>$setup]);

        $customerSetup->addAttribute(Customer::ENTITY, $attributeCode, [
            'type' => 'varchar',
            'label' => 'External Customer Id',
            'input' => 'text',
            'required' => false,
            'visible' => true,
            'position' => 999,
            'system' => 0
        ]);

        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, $attributeCode)
            ->addData([
                'used_in_forms' => ['adminhtml_customer']
            ]);

        $attribute->save();
    }
}