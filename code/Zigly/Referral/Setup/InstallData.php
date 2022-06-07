<?php
namespace Zigly\Referral\Setup;

use Magento\Customer\Model\Customer;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements \Magento\Framework\Setup\InstallDataInterface
{
    private $eavSetupFactory;
    
    private $eavConfig;
    
    private $attributeResource;
    
    public function __construct(
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Customer\Model\ResourceModel\Attribute $attributeResource
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
        $this->attributeResource = $attributeResource;
    }
    
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $attributeSetId = $eavSetup->getDefaultAttributeSetId(Customer::ENTITY);
        $attributeGroupId = $eavSetup->getDefaultAttributeGroupId(Customer::ENTITY);

        //Referral Code
        $eavSetup->addAttribute(Customer::ENTITY, 'referralcode', [
            // Attribute parameters
            'type' => 'varchar',
            'label' => 'Referral Code',
            'input' => 'text',
            'required' => false,
            'visible' => true,
            'user_defined' => true,
            'sort_order' => 990,
            'position' => 990,
            'system' => 0,
        ]);
        
        $attribute = $this->eavConfig->getAttribute(Customer::ENTITY, 'referralcode');
        $attribute->setData('attribute_set_id', $attributeSetId);
        $attribute->setData('attribute_group_id', $attributeGroupId);

        $attribute->setData('used_in_forms', [
            'adminhtml_customer'
        ]);
        $this->attributeResource->save($attribute);

        //Refer Code
        $eavSetup->addAttribute(Customer::ENTITY, 'refercode', [
            // Attribute parameters
            'type' => 'varchar',
            'label' => 'Reference code',
            'input' => 'text',
            'required' => false,
            'visible' => true,
            'user_defined' => true,
            'sort_order' => 991,
            'position' => 991,
            'system' => 0,
        ]);
        
        $attribute = $this->eavConfig->getAttribute(Customer::ENTITY, 'refercode');
        $attribute->setData('attribute_set_id', $attributeSetId);
        $attribute->setData('attribute_group_id', $attributeGroupId);

        $attribute->setData('used_in_forms', [
            'adminhtml_customer'
        ]);

        $this->attributeResource->save($attribute);

        //Referral Type
        $eavSetup->addAttribute(Customer::ENTITY, 'referral_type', [
            // Attribute parameters
            'type' => 'varchar',
            'label' => 'Reference Type',
            'input' => 'select',
            'required' => false,
            'visible' => true,
            'user_defined' => true,
            'sort_order' => 992,
            'position' => 992,
            'system' => 0,
            'source' => 'Zigly\Referral\Model\Source\ReferralOptions'
        ]);
        
        $attribute = $this->eavConfig->getAttribute(Customer::ENTITY, 'referral_type');
        $attribute->setData('attribute_set_id', $attributeSetId);
        $attribute->setData('attribute_group_id', $attributeGroupId);

        $attribute->setData('used_in_forms', [
            'adminhtml_customer'
        ]);

        $this->attributeResource->save($attribute);

        //Referral Value
        $eavSetup->addAttribute(Customer::ENTITY, 'referral_value', [
            // Attribute parameters
            'type' => 'varchar',
            'label' => 'Referral Value',
            'input' => 'text',
            'required' => false,
            'visible' => true,
            'user_defined' => true,
            'sort_order' => 993,
            'position' => 993,
            'system' => 0,
        ]);
        
        $attribute = $this->eavConfig->getAttribute(Customer::ENTITY, 'referral_value');
        $attribute->setData('attribute_set_id', $attributeSetId);
        $attribute->setData('attribute_group_id', $attributeGroupId);

        $attribute->setData('used_in_forms', [
            'adminhtml_customer'
        ]);

        $this->attributeResource->save($attribute);
    }
}
?>