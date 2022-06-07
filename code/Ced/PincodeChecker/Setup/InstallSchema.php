<?php

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_PincodeChecker
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license     https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\PincodeChecker\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;

/**
 * Class InstallSchema
 * @package Ced\PincodeChecker\Setup
 */
class InstallSchema implements InstallSchemaInterface
{

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        /**
         * Create table 'ced_pincode_checker'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('ced_pincode_checker')
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Id'
        )->addColumn(
            'website_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => '0'],
            'Website Id'
        )->addColumn(
            'vendor_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            10,
            ['nullable' => false, 'default' => 'admin'],
            'Vendor Id'
        )->addColumn(
            'zipcode',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            100,
            ['nullable' => false, 'default' => ''],
            'Zip Code'
        )->addColumn(
            'can_cod',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            1,
            ['nullable' => false, 'default' => '1'],
            'Can COD'
        )->addColumn(
            'can_ship',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            1,
            ['nullable' => false, 'default' => '1'],
            'Can Ship'
        )->addColumn(
            'days_to_deliver',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            100,
            ['nullable' => false, 'default' => ''],
            'Days To Deliver'
        )->setComment(
            'Pincode Checker Table'
        );
        $installer->getConnection()->createTable($table);
    }
}
