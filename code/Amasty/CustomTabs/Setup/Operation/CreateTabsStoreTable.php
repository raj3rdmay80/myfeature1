<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomTabs
 */


namespace Amasty\CustomTabs\Setup\Operation;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;
use Amasty\CustomTabs\Api\Data\TabsInterface;

/**
 * Class CreateTabsStoreTable
 */
class CreateTabsStoreTable
{
    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->createTable(
            $this->createTable($setup)
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     *
     * @return Table
     */
    private function createTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getTable(TabsInterface::STORE_TABLE_NAME);
        $tabsTable = $setup->getTable(\Amasty\CustomTabs\Model\Tabs\ResourceModel\Tabs::TABLE_NAME);
        $storeTable = $setup->getTable('store');

        return $setup->getConnection()
            ->newTable(
                $table
            )->setComment(
                'Amasty Product Tabs store relation table'
            )->addColumn(
                TabsInterface::TAB_ID,
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true, 'nullable' => false
                ],
                'Tab Id'
            )->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                [
                    'unsigned' => true, 'nullable' => false
                ],
                'Store Id'
            )->addIndex(
                $setup->getIdxName(
                    $table,
                    [TabsInterface::TAB_ID, 'store_id'],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                [TabsInterface::TAB_ID, 'store_id'],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )->addForeignKey(
                $setup->getFkName(
                    $table,
                    TabsInterface::TAB_ID,
                    $tabsTable,
                    TabsInterface::TAB_ID
                ),
                TabsInterface::TAB_ID,
                $tabsTable,
                TabsInterface::TAB_ID,
                Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName(
                    $table,
                    'store_id',
                    $storeTable,
                    'store_id'
                ),
                'store_id',
                $storeTable,
                'store_id',
                Table::ACTION_CASCADE
            );
    }
}
