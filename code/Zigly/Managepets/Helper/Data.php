<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Managepets
 */
namespace Zigly\Managepets\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Review\Model\ReviewFactory;

class Data extends AbstractHelper
{

    public function __construct(
        Context $context,
        \Zigly\Species\Model\SpeciesFactory $speciesFactory,
        \Zigly\Species\Model\BreedFactory $breedFactory,
        ReviewFactory $reviewFactory,
        \Magento\Customer\Model\SessionFactory $customerSession,
        \Magento\Sales\Model\Order\ItemFactory $itemFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->speciesFactory = $speciesFactory;
        $this->breedFactory = $breedFactory;
        $this->reviewFactory = $reviewFactory;
        $this->_customerSession = $customerSession;
        $this->itemFactory = $itemFactory;
        $this->_registry = $registry;
        parent::__construct($context);

    }

    public function getSpecies($savedspecie = null){
        $savedspecie = ($savedspecie) ? $savedspecie : 0;
        $collection = $this->speciesFactory->create()->getCollection()->addFieldToFilter(
            ['status','species_id'],
            [
                ['eq' =>1],
                ['eq'=>$savedspecie]
            ]
        );
        return $collection;
    }

    public function getBreeds($savedspecie = null,$savedbreed =null){
        $savedbreed = ($savedbreed) ? $savedbreed : 0;
        $savedspecie = ($savedspecie) ? $savedspecie : 0;
        $collection = $this->breedFactory->create()->getCollection()->addFieldToFilter(
            ['main_table.status','main_table.breed_id'],
            [
                ['eq' =>1],
                ['eq'=>$savedbreed]
            ]
        );
        $collection->getSelect()
        ->joinLeft(
            ['species'=>'zigly_species_species'],
            "main_table.species_id = species.species_id",
            [
                'speciesname' => 'species.name'
            ]
        )->joinLeft(
            ['submaintable'=>'zigly_species_breed'],
            "main_table.breed_id = submaintable.breed_id",
            [
                'value' => 'submaintable.name'
            ]
        )->where("species.status = '1' OR species.species_id = '".$savedspecie."'");
        return $collection;
    }
    public function getGenders(){
        $genders = [1=>'Male',2=>"Female"];
        return $genders;
    }
    public function reviewenable(){
        $product = $this->getCurrentProduct();
        $pid =$product->getId();
        $cid = $this->_customerSession->create()->getCustomer()->getId();
        $orderitems = $this->itemFactory->create()->getCollection()->addFieldToFilter('main_table.product_id',$pid);
        $orderitems->getSelect()
        ->joinLeft(
            ['order'=>'sales_order'],
            "main_table.order_id = order.entity_id",
            [
                'ordercustomer_id' => 'order.customer_id'
            ]
        )->where("order.customer_id = '".$cid."'");
        $collection = $this->reviewFactory->create()->getCollection()->addFieldToFilter('main_table.entity_pk_value',$pid)->addFieldToFilter('main_table.entity_id',1)->addFieldToFilter('detail.customer_id',$cid);
        if($orderitems->getSize() && !$collection->getSize()){
            return true;
        }
        return false;
    }
    public function getCurrentProduct()
    {
        return $this->_registry->registry('current_product');
    }

}
