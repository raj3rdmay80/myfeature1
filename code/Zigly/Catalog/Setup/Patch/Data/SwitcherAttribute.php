<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Catalog
 */

namespace Zigly\Catalog\Setup\Patch\Data;

use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Config;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class SwitcherAttribute implements DataPatchInterface
{
    /**
     * @var CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
     * @var ModuleDataSetupInterface
     */
    private $setup;

    /**
     * @var Config
     */
    private $eavConfig;

    /**
     * AccountPurposeCustomerAttribute constructor.
     * @param ModuleDataSetupInterface $setup
     * @param Config $eavConfig
     * @param CustomerSetupFactory $customerSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $setup,
        Config $eavConfig,
        EavSetupFactory $eavSetupFactory
    )
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->setup = $setup;
        $this->eavConfig = $eavConfig;
    }

    /** We'll add our customer attribute here */
    public function apply()
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->setup]);
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Category::ENTITY, 'include_in_switcher');
        $eavSetup -> addAttribute(\Magento\Catalog\Model\Category :: ENTITY, 'include_in_switcher', [
                    'type' => 'int',
                    'label' => 'Include In Switcher',
                    'input' => 'select',
                    'required' => false,
                    'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                    'sort_order' => 110,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'group' => 'General Information',
                    "default" => "",
                    "class"    => "",
                    "note"       => ""
        ]
        );
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }
}
