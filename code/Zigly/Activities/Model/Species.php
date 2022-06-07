<?php
/**
* Copyright (C) 2021  Zigly
* @package   Zigly_Activities
*/
namespace Zigly\Activities\Model;

use Magento\Framework\Data\OptionSourceInterface;
use Zigly\Species\Model\ResourceModel\Species\CollectionFactory;

class Species implements OptionSourceInterface
{

    /**
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Get species row type array for option element.
     * @return array
     */
    public function toOptionArray()
    {
        $res = [];
        $speciesCollection = $this->collectionFactory->create();
        foreach ($speciesCollection as $index => $value) {
            $res[] = ['value' => $value['species_id'], 'label' => $value['name']];
        }
        return $res;
    }

}
