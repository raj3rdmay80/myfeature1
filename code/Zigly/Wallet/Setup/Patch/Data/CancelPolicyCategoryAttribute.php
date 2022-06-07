<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Wallet
 */
declare(strict_types=1);

namespace Zigly\Wallet\Setup\Patch\Data;

// use Magento\Customer\Model\Customer;
// use Magento\Customer\Setup\CustomerSetup;
// use Magento\Customer\Setup\CustomerSetupFactory;
// use Magento\Eav\Model\Entity\Attribute\Set;
// use Magento\Eav\Model\Entity\Attribute\SetFactory;
// use Magento\Framework\Setup\ModuleDataSetupInterface;
// use Magento\Framework\Setup\Patch\DataPatchInterface;
// use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class CancelPolicyCategoryAttribute implements DataPatchInterface
{

    /**
     * ModuleDataSetupInterface
     *
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;
    /**
     * EavSetupFactory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;
    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory          $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }
    /**
     * {@inheritdoc}
     */
    public function apply() {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $eavSetup->addAttribute(\Magento\Catalog\Model\Category::ENTITY, 'cancel_policy', [
            'type' => 'int',
            'label' => 'Cancel Item Policy',
            'input' => 'int',
            'default' => 0,
            'sort_order' => 5,
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'group' => 'General Information',
            'visible_on_front' => true
        ]);
    }
    /**
     * {@inheritdoc}
     */
    public static function getDependencies() {
        return [];
    }
    /**
     * {@inheritdoc}
     */
    public function getAliases() {
        return [];
    }
}