<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomTabs
 */


namespace Amasty\CustomTabs\Setup\Operation;

use Amasty\CustomTabs\Api\Data\TabsInterface;
use Amasty\CustomTabs\Model\Tabs\ResourceModel\RuleIndex;
use Amasty\CustomTabs\Model\Tabs\ResourceModel\Tabs;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class CreateTabsIndexTable
 */
class CreateTabsIndexTable
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

    public function createTable(SchemaSetupInterface $setup)
    {
        $tableName = $setup->getTable(RuleIndex::MAIN_TABLE);

        return $setup->getConnection()
            ->newTable($tableName)
            ->addColumn(
                RuleIndex::TAB_ID,
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Tab ID'
            )->addColumn(
                RuleIndex::STORE_ID,
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Store ID'
            )->addColumn(
                RuleIndex::PRODUCT_ID,
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Product ID'
            )->addIndex(
                $setup->getIdxName(RuleIndex::MAIN_TABLE, [RuleIndex::TAB_ID, RuleIndex::STORE_ID]),
                [RuleIndex::TAB_ID, RuleIndex::STORE_ID]
            )->addForeignKey(
                $setup->getFkName(
                    RuleIndex::MAIN_TABLE,
                    RuleIndex::PRODUCT_ID,
                    'catalog_product_entity',
                    'entity_id'
                ),
                RuleIndex::PRODUCT_ID,
                $setup->getTable('catalog_product_entity'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName(
                    RuleIndex::MAIN_TABLE,
                    RuleIndex::TAB_ID,
                    Tabs::TABLE_NAME,
                    TabsInterface::TAB_ID
                ),
                RuleIndex::TAB_ID,
                $setup->getTable(Tabs::TABLE_NAME),
                TabsInterface::TAB_ID,
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName(
                    RuleIndex::MAIN_TABLE,
                    RuleIndex::STORE_ID,
                    'store',
                    'store_id'
                ),
                RuleIndex::STORE_ID,
                $setup->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment(
                'Amasty Custom Tabs Rule Index Table'
            );
    }
}
