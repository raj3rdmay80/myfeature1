<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Cityscreen
 */
namespace Zigly\Cityscreen\Setup;

use Magento\Customer\Model\Customer;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Eav\Setup\EavSetup;

class UpgradeData implements UpgradeDataInterface
{

    /**
     * @var EavSetup
     */
    private $eavSetupFactory;

    /**
     * @param EavSetup $eavSetupFactory
     */
    public function __construct(
        EavSetup $eavSetupFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    // @codingStandardsIgnoreStart
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '0.0.1') < 0) {
            $this->eavSetupFactory->updateAttribute(Customer::ENTITY, "city_screen", 'is_visible', 0);
        }
        $setup->endSetup();
    }
}
