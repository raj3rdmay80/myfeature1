<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Cityscreen
 */
declare(strict_types=1);

namespace Zigly\Cityscreen\Block;

class Cityscreen extends \Magento\Framework\View\Element\Template
{

    /**
     * @var $cityscreenCollectionFactory
     */
    protected $cityscreenCollectionFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Zigly\Cityscreen\Model\ResourceModel\Cityscreen\CollectionFactory $cityscreenCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Zigly\Cityscreen\Model\ResourceModel\Cityscreen\CollectionFactory $cityscreenCollectionFactory,
        array $data = []
    )
    {
        $this->cityscreenCollectionFactory = $cityscreenCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * get city by id
     * @return void
     */
    public function getCityById($id)
    {
        $collection = $this->cityscreenCollectionFactory->create();
        $collection->addFieldToFilter('is_active', ['eq' => '1']);
        $collection->addFieldToFilter('type', ['eq' => $id]);
        /*$collection->getColumnValues('city');*/
        $collection->addFieldToSelect('city');
        $collection->addFieldToSelect('pincode');
        $cityPincode = [];
        foreach ($collection->getData() as $value) {
            $cityPincode[] = ''.$value['city'].', '.$value['pincode'].'';
        }
        return $cityPincode;
    }

    /**
     * get delhi city by id
     * @return void
     */
    public function getDelhiCityById()
    {
        $collection = $this->cityscreenCollectionFactory->create();
        $collection->addFieldToFilter('is_active', ['eq' => '1']);
        $collection->addFieldToFilter('type', ['eq' => '1']);
        $collection->addFieldToSelect('city');
        $collection->addFieldToSelect('pincode');
        $delhiPincode = [];
        foreach ($collection->getData() as $value) {
            $delhiPincode[] = ''.$value['city'].', '.$value['pincode'].'';
        }
        return $delhiPincode;
    }
}