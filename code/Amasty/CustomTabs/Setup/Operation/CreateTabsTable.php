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
 * Class CreateTabsTable
 */
class CreateTabsTable
{
    /**
     * @param SchemaSetupInterface $setup
     *
     * @throws \Zend_Db_Exception
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->createTable(
            $this->createTabsTable($setup)
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     *
     * @return Table
     * @throws \Zend_Db_Exception
     */
    private function createTabsTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getTable(\Amasty\CustomTabs\Model\Tabs\ResourceModel\Tabs::TABLE_NAME);
        
        return $setup->getConnection()
            ->newTable(
                $table
            )->setComment(
                'Amasty Product tabs table'
            )->addColumn(
                TabsInterface::TAB_ID,
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true
                ],
                'Tab Id'
            )->addColumn(
                TabsInterface::SORT_ORDER,
                Table::TYPE_SMALLINT,
                null,
                [
                    'unsigned' => true, 'nullable' => true
                ],
                'Sort order'
            )->addColumn(
                TabsInterface::TAB_TITLE,
                Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false
                ],
                'Tab title'
            )->addColumn(
                TabsInterface::TAB_NAME,
                Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false
                ],
                'Tab name'
            )->addColumn(
                TabsInterface::CONTENT,
                Table::TYPE_TEXT,
                null,
                [
                    'nullable' => true
                ],
                'Content'
            )->addColumn(
                TabsInterface::CUSTOMER_GROUPS,
                Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false
                ],
                'Customer Groups'
            )->addColumn(
                TabsInterface::STATUS,
                Table::TYPE_BOOLEAN,
                null,
                [
                    'default' => false, 'unsigned' => true, 'nullable' => false
                ],
                'Status'
            )->addColumn(
                TabsInterface::IS_ACTIVE,
                Table::TYPE_BOOLEAN,
                null,
                [
                    'default' => false, 'unsigned' => true, 'nullable' => false
                ],
                'Is active'
            )->addColumn(
                TabsInterface::RELATED_ENABLED,
                Table::TYPE_BOOLEAN,
                null,
                [
                    'default' => false, 'unsigned' => true, 'nullable' => false
                ],
                'Is RELATED ENABLED'
            )->addColumn(
                TabsInterface::UPSELL_ENABLED,
                Table::TYPE_BOOLEAN,
                null,
                [
                    'default' => false, 'unsigned' => true, 'nullable' => false
                ],
                'Is UPSELL ENABLED'
            )->addColumn(
                TabsInterface::CROSSSELL_ENABLED,
                Table::TYPE_BOOLEAN,
                null,
                [
                    'default' => false, 'unsigned' => true, 'nullable' => false
                ],
                'Is CROSSSELL ENABLED'
            )->addColumn(
                TabsInterface::CREATED_AT,
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Created At'
            )->addColumn(
                TabsInterface::UPDATED_AT,
                Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE
                ],
                'Updated At'
            )->addColumn(
                'conditions_serialized',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '2M',
                [],
                'Conditions Serialized'
            )->addColumn(
                TabsInterface::TAB_TYPE,
                Table::TYPE_SMALLINT,
                null,
                [
                    'default' => 0, 'unsigned' => true, 'nullable' => false
                ],
                'Tab type'
            )->addColumn(
                TabsInterface::NAME_IN_LAYOUT,
                Table::TYPE_TEXT,
                50,
                [
                    'nullable' => true
                ],
                'Name in Layout (for defualt tabs)'
            )->addColumn(
                TabsInterface::MODULE_NAME,
                Table::TYPE_TEXT,
                50,
                [
                    'nullable' => true
                ],
                'Module name for default and 3rd patry extensions'
            );
    }
}
