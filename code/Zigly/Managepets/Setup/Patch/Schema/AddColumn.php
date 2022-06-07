<?php
/**
* Copyright © 2019 Zigly_Managepets. All rights reserved.
* See COPYING.txt for license details.
*/
namespace Zigly\Managepets\Setup\Patch\Schema;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class AddColumn implements SchemaPatchInterface
{
  private $moduleDataSetup;
  public function __construct(
    ModuleDataSetupInterface $moduleDataSetup
  ) {
    $this->moduleDataSetup = $moduleDataSetup;
  }
  public static function getDependencies()
   {
       return [];
   }


   public function getAliases()
   {
       return [];
   }


   public function apply()
   {
       $this->moduleDataSetup->startSetup();


       $this->moduleDataSetup->getConnection()->addColumn(
           $this->moduleDataSetup->getTable('zigly_managepets'),
           'enable_species',
           [
               'type' => Table::TYPE_INTEGER,
               'nullable' => true,
               'default' => 0,
               'comment'  => 'Enable Species',
           ]
       );
      $this->moduleDataSetup->getConnection()->addColumn(
           $this->moduleDataSetup->getTable('zigly_managepets'),
           'enable_breed',
           [
               'type' => Table::TYPE_INTEGER,
               'nullable' => true,
               'default' => 0,
               'comment'  => 'Enable Breed',
           ]
       );


       $this->moduleDataSetup->endSetup();
   }
}
