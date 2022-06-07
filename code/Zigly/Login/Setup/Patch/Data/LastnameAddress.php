<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Login
 */

namespace Zigly\Login\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetup;
use Magento\Customer\Model\Customer;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/** 
 * Remove  required lastname 
*/
class LastnameAddress implements DataPatchInterface
{

    /**
    *  @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /** 
    * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
    * @param ModuleDataSetupInterface $moduleDataSetup
    * @param EavSetupFactory $eavSetupFactory
    */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetup $eavSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavSetup = $eavSetup;
    }

    /**
    * {@inheritdoc}
    */
    public function apply()
    {
       /** @var EavSetup $eavSetup */
        $this->eavSetup->updateAttribute('customer_address', "lastname", 'is_required', 0);
        /*$this->eavSetup->updateAttribute('customer_address', "lastname", 'is_visible', 0);*/
        /*$this->eavSetup->updateAttribute('customer_address', "firstname", 'frontend_label', 'Full Name');*/
    }

    /**
    * {@inheritdoc}
    */
    public static function getDependencies()
    {
        return [];
    }

    /**
    * {@inheritdoc}
    */
    public function getAliases()
    {
        return [];
    }

}