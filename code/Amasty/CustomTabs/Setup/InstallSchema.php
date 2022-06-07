<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomTabs
 */


namespace Amasty\CustomTabs\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Amasty\CustomTabs\Setup\Operation;

/**
 * Class InstallSchema
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var Operation\CreateTabsTable
     */
    private $createTabsTable;

    /**
     * @var Operation\CreateTabsStoreTable
     */
    private $createTabsStoreTable;

    /**
     * @var Operation\CreateTabsIndexTable
     */
    private $createTabsIndexTable;

    public function __construct(
        Operation\CreateTabsTable $createTabsTable,
        Operation\CreateTabsStoreTable $createTabsStoreTable,
        Operation\CreateTabsIndexTable $createTabsIndexTable
    ) {
        $this->createTabsTable = $createTabsTable;
        $this->createTabsStoreTable = $createTabsStoreTable;
        $this->createTabsIndexTable = $createTabsIndexTable;
    }

    /**
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $this->createTabsTable->execute($setup);
        $this->createTabsStoreTable->execute($setup);
        $this->createTabsIndexTable->execute($setup);
        $setup->endSetup();
    }
}
