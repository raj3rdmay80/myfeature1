<?php

namespace Magecomp\Gstcharge\Setup;

use Magento\Customer\Model\Customer;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Setup\CustomerSetupFactory;

class UpgradeData implements  UpgradeDataInterface
{
	private $customerSetupFactory;

    public function __construct(CustomerSetupFactory $customerSetupFactory)
    {
        $this->customerSetupFactory = $customerSetupFactory;
    }
    public function upgrade(ModuleDataSetupInterface $setup,
                            ModuleContextInterface $context)
	{
		$setup->startSetup();
		if (version_compare($context->getVersion(), '1.0.3') < 0) 
		{
			$customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
            $customerSetup->addAttribute(
                Customer::ENTITY,
                'buyergst',
                [
                    'label' => 'Buyer GST Number',
                    'required' => 0,
                    'system' => 0,
                    'position' => 100
                ]
            );
           	$attribute = $customerSetup->getEavConfig()->getAttribute('customer', 'buyergst');
		
			$used_in_forms[]="adminhtml_customer";
			$used_in_forms[]="checkout_register";
			$used_in_forms[]="customer_account_create";
			$used_in_forms[]="customer_account_edit";
			$used_in_forms[]="adminhtml_checkout";
			
			$attribute->setData('used_in_forms', $used_in_forms)
					->setData("is_used_for_customer_segment", true)
					->setData("is_system", 0)
					->setData("sort_order", 200);
			$attribute->save();
        }
        $setup->endSetup();
    }
}