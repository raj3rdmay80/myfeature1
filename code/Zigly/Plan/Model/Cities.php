<?php
/**
* Copyright (C) 2021  Zigly
* @package   Zigly_Plan
*/
namespace Zigly\Plan\Model;

use Magento\Framework\Data\OptionSourceInterface;
use Zigly\Cityscreen\Model\ResourceModel\Cityscreen\CollectionFactory;

class Cities implements OptionSourceInterface
{

    /**
     * @param CollectionFactory $cityCollectionFactory
     */
    public function __construct(
        CollectionFactory $cityCollectionFactory
    ) {
        $this->cityCollectionFactory = $cityCollectionFactory;
    }

    /**
     * Get cities row type array for option element.
     * @return array
     */
    public function getOptions()
    {
        $res = [];
        $cityCollection = $this->cityCollectionFactory->create()->addFieldToSelect( ['cityscreen_id', 'city']);
        $cityCollection->getSelect()->group('city');
        foreach ($cityCollection as $city) {
            $res[] = ['value' => $city->getData('cityscreen_id'), 'label' => $city->getData('city')];
        }
        return $res;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return $this->getOptions();
    }

}
