<?php 

namespace CommerceExtensions\ProductImportExport\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * upgrades DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $tableName = $setup->getTable('productimportexport_cronjobdata');
        if (version_compare($context->getVersion(), '1.0.4', '<')) {
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $connection = $setup->getConnection();

                  // Declare data
                $columns = [
                    'apply_additional_filters' => [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'default' => null,
                  		'length' =>100,
                        'comment' => 'Apply Additional Filters',
                        'after' => 'Profile_type',
                    ],
                    'filter_qty_from' => [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'default' => null,
                  		'length' =>100,
                        'comment' => 'Filter Qty From',
                        'after' => 'Profile_type',
                    ],
                    'filter_qty_to' => [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'default' => null,
                  		'length' =>100,
                        'comment' => 'Filter Qty To',
                        'after' => 'Profile_type',
                    ],
                    'append_websites' => [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'default' => null,
                  		'length' =>100,
                        'comment' => 'Append Websites',
                        'after' => 'append_categories',
                    ],
                    'append_grouped_products' => [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'default' => null,
                  		'length' =>100,
                        'comment' => 'Append Group Products',
                        'after' => 'append_categories',
                    ],
                    'ref_by_product_id' => [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'default' => null,
                  		'length' =>100,
                        'comment' => 'Lookup By ProductID',
                        'after' => 'update_products_only',
                    ],
                    'import_attribute_value' => [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'default' => null,
                  		'length' =>100,
                        'comment' => 'Import Attribute Value',
                        'after' => 'update_products_only',
                    ],
                    'attribute_for_import_value' => [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'default' => null,
                  		'length' =>100,
                        'comment' => 'Attribute To Import Value',
                        'after' => 'update_products_only',
                    ],
                    'export_file_name' => [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'default' => null,
                  		'length' =>200,
                        'comment' => 'Cronjob Export File Name',
                        'after' => 'export_full_image_paths',
                    ],
                    'export_multi_store' => [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'default' => null,
                  		'length' =>100,
                        'comment' => 'Export Multi Store Data',
                        'after' => 'export_full_image_paths',
                    ],
                    'data_transfer' => [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'default' => null,
                  		'length' =>100,
                        'comment' => 'Data Transfer',
                        'after' => 'export_full_image_paths',
                    ],
                    'remote_file_name' => [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'default' => null,
                  		'length' =>200,
                        'comment' => 'Remote File Name',
                        'after' => 'export_full_image_paths',
                    ],
                    'remote_file_path' => [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'default' => null,
                  		'length' =>200,
                        'comment' => 'Remote File Path',
                        'after' => 'export_full_image_paths',
                    ],
                    'remote_host' => [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'default' => null,
                  		'length' =>200,
                        'comment' => 'Remote Host',
                        'after' => 'export_full_image_paths',
                    ],
                    'remote_user_name' => [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'default' => null,
                  		'length' =>200,
                        'comment' => 'Remote User Name',
                        'after' => 'export_full_image_paths',
                    ],
                    'remote_password' => [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'default' => null,
                  		'length' =>100,
                        'comment' => 'Remote Password',
                        'after' => 'export_full_image_paths',
                    ],
                ];

                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }
        }
        if (version_compare($context->getVersion(), '1.0.5', '<')) {
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $connection = $setup->getConnection();
				  // Declare data
                $columns = [
                    'auto_create_categories' => [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'default' => null,
                  		'length' =>100,
                        'comment' => 'Auto Create Categories',
                        'after' => 'append_websites',
                    ],
                    'import_fields' => [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'default' => null,
                  		'length' =>100,
                        'comment' => 'Import Fields',
                        'after' => 'auto_create_categories',
                    ],
                ];

                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
			}
		}
        if (version_compare($context->getVersion(), '1.1.5', '<=')) {
			$table = $setup->getConnection()
				->newTable($setup->getTable('productimportexport_profiledata'))
				->addColumn(
					'profile_id',
					Table::TYPE_SMALLINT,
					null,
					['identity' => true, 'nullable' => false, 'primary' => true],
					'Profile ID'
				)
				->addColumn('profile_type', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
				->addColumn('import_delimiter', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
				->addColumn('import_enclose', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
				->addColumn('root_catalog_id', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
				->addColumn('enable_default_magento_format', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
				->addColumn('import_attribute_value', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
				->addColumn('attribute_for_import_value', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
				->addColumn('ref_by_product_id', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
				->addColumn('create_products_only', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
				->addColumn('update_products_only', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
				->addColumn('import_images_by_url', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
				->addColumn('reimport_images', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
				->addColumn('deleteall_andreimport_images', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
				->addColumn('append_websites', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
				->addColumn('append_grouped_products', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
				->addColumn('append_tier_prices', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
				->addColumn('append_categories', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
				->addColumn('auto_create_categories', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
				->addColumn('import_fields', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
				->addColumn('import_fields_mapped', Table::TYPE_TEXT, '64k', ['nullable' => true, 'default' => null])
				->addColumn('export_delimiter', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
				->addColumn('export_enclose', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
				->addColumn('export_manual_file_name', Table::TYPE_TEXT, 255, ['nullable' => true, 'default' => null])
				->addColumn('export_fields', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
				->addColumn('export_fields_mapped', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
				->addColumn('product_id_from', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
				->addColumn('product_id_to', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
				->addColumn('apply_additional_filters', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
				->addColumn('export_filter_by_attribute_code', Table::TYPE_TEXT, 255, ['nullable' => true, 'default' => null])
				->addColumn('export_filter_by_attribute_value', Table::TYPE_TEXT, 255, ['nullable' => true, 'default' => null])
				->addColumn('filter_qty_from', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
				->addColumn('filter_qty_to', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
				->addColumn('export_filter_by_categoryids', Table::TYPE_TEXT, 255, ['nullable' => true, 'default' => null])
				->addColumn('export_filter_by_skus', Table::TYPE_TEXT, 255, ['nullable' => true, 'default' => null])
				->addColumn('export_filter_by_product_type', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
				->addColumn('filter_status', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
				->addColumn('export_filter_by_attribute_set', Table::TYPE_TEXT, 255, ['nullable' => true, 'default' => null])
				->addColumn('export_multi_store', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
				->addColumn('export_grouped_position', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
				->addColumn('export_related_position', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
				->addColumn('export_crossell_position', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
				->addColumn('export_upsell_position', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
				->addColumn('export_category_paths', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
				->addColumn('export_full_image_paths', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
				->addColumn('creation_time', Table::TYPE_DATETIME, null, ['nullable' => false], 'Creation Time')
				->addColumn('update_time', Table::TYPE_DATETIME, null, ['nullable' => false], 'Update Time')
				->setComment('CommerceExtensions Product ImportExport Profiles');
	
			$setup->getConnection()->createTable($table);
		
		}
        if (version_compare($context->getVersion(), '1.1.7', '<=')) {
			
            if ($setup->getConnection()->isTableExists($setup->getTable('productimportexport_profiledata')) == true) {
                $connection = $setup->getConnection();
				  // Declare data
                $columns = [
                    'append_grouped_products' => [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'default' => null,
                  		'length' =>255,
                        'comment' => 'Append Group Products',
                        'after' => 'append_websites',
                    ],
                ];

                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($setup->getTable('productimportexport_profiledata'), $name, $definition);
                }
			}
		
		}
        if (version_compare($context->getVersion(), '1.1.10', '<=')) {
			
            if ($setup->getConnection()->isTableExists($setup->getTable('productimportexport_profiledata')) == true) {
                $connection = $setup->getConnection();
				  // Declare data
                $columns = [
                    'export_fields_mapped' => [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'default' => null,
                  		'length' => '64k',
                        'comment' => 'Export Mapped Fields',
                        'after' => 'append_websites',
                    ],
                ];

                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($setup->getTable('productimportexport_profiledata'), $name, $definition);
                }
			}
		
		}
        if (version_compare($context->getVersion(), '1.1.2', '<')) {
			
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $connection = $setup->getConnection();
				  // Declare data
                $columns = [
                    'export_filter_by_categoryids' => [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'default' => null,
                  		'length' =>255,
                        'comment' => 'Export Fields',
                        'after' => 'export_multi_store',
                    ],
                    'export_filter_by_skus' => [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'default' => null,
                  		'length' =>255,
                        'comment' => 'Export Fields',
                        'after' => 'export_multi_store',
                    ],
                    'export_filter_by_product_type' => [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'default' => null,
                  		'length' =>255,
                        'comment' => 'Export Fields',
                        'after' => 'export_multi_store',
                    ],
                    'export_filter_by_attribute_set' => [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'default' => null,
                  		'length' =>255,
                        'comment' => 'Export Fields',
                        'after' => 'export_multi_store',
                    ],
                    'export_filter_by_attribute_code' => [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'default' => null,
                  		'length' =>255,
                        'comment' => 'Export Fields',
                        'after' => 'export_multi_store',
                    ],
                    'export_filter_by_attribute_value' => [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'default' => null,
                  		'length' =>255,
                        'comment' => 'Export Fields',
                        'after' => 'export_multi_store',
                    ],
                    'enable_default_magento_format' => [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'default' => null,
                  		'length' =>100,
                        'comment' => 'Import Fields',
                        'after' => 'root_catalog_id',
                    ],
                    'create_products_only' => [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'default' => null,
                  		'length' =>100,
                        'comment' => 'Import Fields',
                        'after' => 'root_catalog_id',
                    ],
                ];

                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
			}
		
		}
        $setup->endSetup();

    }

}