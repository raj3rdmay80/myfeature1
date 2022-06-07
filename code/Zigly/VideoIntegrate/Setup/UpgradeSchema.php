<?php  
namespace Zigly\VideoIntegrate\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
	    $setup->startSetup();
        $conn = $setup->getConnection();
        if (version_compare($context->getVersion(), '1.3.0', '<')) {
            $tableName = $setup->getTable('twilio_data');

                $table = $conn->newTable($tableName)
                ->addColumn(
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    11,
                    ['identity'=>true,'unsigned'=>true,'nullable'=>false,'primary'=>true]
                    )  
                ->addColumn(
				    'token_vet',
				    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				    255,
				    [],
				    'token_vet'
			    )  
				->addColumn(
				    'token_customer',
				    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				    255,
				    [],
				    'token_customer'
			    )  
				->addColumn(
				    'identity_vet',
				    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				    255,
				    [],
				    'identity_vet'
			    )  
				->addColumn(
				    'identity_customer',
				    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				    255,
				    [],
				    'identity_customer'
			    )  
				->addColumn(
				    'room_name',
				    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				    255,
				    [],
				    'room_name'
			    )
			    ->addColumn(
				    'order_id',
				    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				    255,
				    [],
				    'order_id'
			    )
				->addColumn(
				    'customer_id',
				    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				    255,
				    [],
				    'customer_id'
			    )
				->addColumn(
				    'vet_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    25,
                    [],
                    'vet_id'
                    )
				->addColumn(
				    'status',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    25,
                    [],
                    'status'
                    )
                ->addColumn(
				    'created_at',
				    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
				    null,
				    ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
				    'Created At'
			    )
			    ->addColumn(
				    'updated_at',
				    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
				    null,
				    ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
				    'Updated At'
			    )
            ->setOption('charset','utf8');
            $conn->createTable($table);
            }
				
        $setup->endSetup();
	}
}
