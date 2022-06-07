<?php 

namespace CommerceExtensions\ProductImportExport\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class Recurring  implements InstallSchemaInterface
{
    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
       $installer = $setup;

        $installer->startSetup();
		# create table productimportexport_cronjobdata 
        $table = $installer->getConnection()
            ->newTable($installer->getTable('productimportexport_cronjobdata'))
            ->addColumn(
                'post_id',
                Table::TYPE_SMALLINT,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Post ID'
            )
            ->addColumn('root_catalog_id', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
            ->addColumn('enable_default_magento_format', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
			->addColumn('create_products_only', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
            ->addColumn('update_products_only', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
            ->addColumn('ref_by_product_id', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
            ->addColumn('import_attribute_value', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
            ->addColumn('attribute_for_import_value', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
            ->addColumn('import_images_by_url', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
            ->addColumn('reimport_images', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
            ->addColumn('deleteall_andreimport_images', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
            ->addColumn('append_tier_prices', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
            ->addColumn('append_categories', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
            ->addColumn('append_websites', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
			->addColumn('append_grouped_products', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
            ->addColumn('auto_create_categories', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
            ->addColumn('import_fields', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
			->addColumn('import_fields_mapped', Table::TYPE_TEXT, '64k', ['nullable' => true, 'default' => null])
            ->addColumn('import_rates_file', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
            ->addColumn('import_delimiter', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
            ->addColumn('import_enclose', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
            ->addColumn('export_delimiter', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
            ->addColumn('export_enclose', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
            ->addColumn('export_fields', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
            ->addColumn('Schedule', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
            ->addColumn('Profile_type', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
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
            ->addColumn('product_id_from', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
            ->addColumn('product_id_to', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
            ->addColumn('export_grouped_position', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
            ->addColumn('export_related_position', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
            ->addColumn('export_crossell_position', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
            ->addColumn('export_upsell_position', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
            ->addColumn('export_category_paths', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
            ->addColumn('export_full_image_paths', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
            ->addColumn('export_multi_store', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
            ->addColumn('import_file_name', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
            ->addColumn('import_file_path', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
            ->addColumn('export_file_name', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
            ->addColumn('export_file_path', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
            ->addColumn('is_active', Table::TYPE_SMALLINT, null, ['nullable' => false, 'default' => '1'], 'Is Job Active?')
            ->addColumn('creation_time', Table::TYPE_DATETIME, null, ['nullable' => false], 'Creation Time')
            ->addColumn('update_time', Table::TYPE_DATETIME, null, ['nullable' => true], 'Update Time')
			->addColumn('data_transfer', Table::TYPE_DATETIME, null, ['nullable' => true], 'Cron Data Transfer')
			->addColumn('remote_file_name', Table::TYPE_DATETIME, null, ['nullable' => true], 'Cron Remote File Name')
			->addColumn('remote_file_path', Table::TYPE_DATETIME, null, ['nullable' => true], 'Cron Remote File Path')
			->addColumn('remote_host', Table::TYPE_DATETIME, null, ['nullable' => true], 'Cron Remote Host')
			->addColumn('remote_user_name', Table::TYPE_DATETIME, null, ['nullable' => true], 'Cron Remote User Name')
			->addColumn('remote_password', Table::TYPE_DATETIME, null, ['nullable' => true], 'Cron Remote Password')
            ->setComment('CommerceExtensions Product ImportExport');

        $installer->getConnection()->createTable($table);
		
		# create table productimportexport_uploadedfiledata 
		
		$table_second = $installer->getConnection()
            ->newTable($installer->getTable('productimportexport_uploadedfiledata'))
            ->addColumn(
                'post_id',
                Table::TYPE_SMALLINT,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Post ID'
            )
			->addColumn('file_name', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
            ->addColumn('file_type', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
            ->addColumn('file_size', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
            ->addColumn('file_uploaded_path', Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null])
           ->addIndex($installer->getIdxName('blog_post', ['file_name']), ['file_name'])
            ->setComment('productimportexport_uploadedfiledata');
			
			$installer->getConnection()->createTable($table_second);
		
        $installer->endSetup();
    }

}