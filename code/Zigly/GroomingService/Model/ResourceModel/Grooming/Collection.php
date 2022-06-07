<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomingService
 */
declare(strict_types=1);

namespace Zigly\GroomingService\Model\ResourceModel\Grooming;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        // $this->_init(
        //     \Zigly\GroomingService\Model\Grooming::class,
        //     \Zigly\GroomingService\Model\ResourceModel\Grooming::class
       // );
        $this->_init(
      'Magento\Framework\View\Element\UiComponent\DataProvider\Document', 'Zigly\GroomingService\Model\ResourceModel\Grooming'
            );
    }
    protected function _initSelect()
    {
          $this->addFilterToMap('entity_id', 'main_table.entity_id');
          $this->addFilterToMap('customer_id', 'main_table.customer_id');
          $this->addFilterToMap('increment_id', 'sales_ordertable.increment_id');
          $this->addFilterToMap('firstname', 'customer_entitytable.firstname');
         $this->addFilterToMap('city', 'main_table.city');
        $this->addFilterToMap('created_at', 'main_table.created_at');


        parent::_initSelect();

           $this->getSelect()->joinLeft(
            ['customer_entitytable' => $this->getTable('customer_entity')], //2nd table name by which you want to join main table
            'main_table.customer_id = customer_entitytable.entity_id',['firstname','lastname','email'])->joinLeft(
             ['customer_address' => $this->getTable('customer_address_entity')], 
            'main_table.customer_id = customer_address.parent_id',  
            ['telephone'])->joinLeft(
             ['zigly_species_speciestable' => $this->getTable('zigly_species_species')], 
            'main_table.pet_species = zigly_species_speciestable.species_id',  
            ['speciesname'=>'name'])->joinLeft(
             ['zigly_species_breedtable' => $this->getTable('zigly_species_breed')], 
            'main_table.pet_breed = zigly_species_breedtable.breed_id',  
            ['breedname'=>'name'])->joinLeft(
             ['zigly_groomer_groomertable' => $this->getTable('zigly_groomer_groomer')], //2nd table name by which you want to join mail table
            'main_table.groomer_id = zigly_groomer_groomertable.groomer_id',['name'])->joinLeft(
             ['sales_ordertable' => $this->getTable('sales_order')], //2nd table name by which you want to join mail table
            'main_table.entity_id = sales_ordertable.booking_id',
            ['increment_id']);
            
    }

}
