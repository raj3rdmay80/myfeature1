<?php
/**
* Copyright (C) 2021  Zigly
* @package   Zigly_Plan
*/
namespace Zigly\Plan\Model;

use Magento\Framework\App\Request\Http;
use Magento\Framework\Data\OptionSourceInterface;
use Zigly\Species\Model\ResourceModel\Species\CollectionFactory;

class Breed implements OptionSourceInterface
{

    /**
     * @param Http $request
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Http $request,
        CollectionFactory $collectionFactory
    ) {
        $this->request = $request;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Get species row type array for option element.
     * @return array
     */
    public function toOptionArray()
    {
        $res = [];
        $breed = $this->request->getParam('breed');
        $speciesCollection = $this->collectionFactory->create();
        if ($breed) {
            $speciesCollection->addFieldToFilter('species_id', ['eq' => $breed]);
        }
        foreach ($speciesCollection as $index => $value) {
            $res[] = ['value' => $value['species_id'], 'label' => $value['name']];
        }
        return $res;
    }

}
