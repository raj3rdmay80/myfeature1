<?php

namespace Zigly\Groomingapi\Setup;

use Magento\Eav\Model\Config;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface 
{
    private $eavSetupFactory;

    public function __construct(
        EavSetupFactory $eavSetupFactory,
        Config $eavConfig
    ) 
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context) 
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);


        // Dropdown Field
        $eavSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, 'address_type', [
            'label' => 'Address Type',
            'system' => 0,
            'position' => 710,
            'sort_order' => 710,
            'visible' => true,
            'note' => 'address type',
            'type' => 'int',
            'input' => 'select',
            'source' => 'Zigly\Groomingapi\Model\Source\Customdropdown',
            ]
        );

        $this->getEavConfig()->getAttribute('customer', 'address_type')->setData('is_user_defined', 1)->setData('is_required', 0)->setData('default_value', '')->setData('used_in_forms', ['adminhtml_customer', 'checkout_register', 'customer_account_create', 'customer_account_edit', 'adminhtml_checkout'])->save();
    }
    
    public function getEavConfig() {
        return $this->eavConfig;
    }
}