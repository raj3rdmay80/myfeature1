<?php
/**
* Copyright (C) 2021  Zigly
* @package   Zigly_Groomer
*/
namespace Zigly\Groomer\Model;

use Magento\Framework\Data\OptionSourceInterface;

class State implements OptionSourceInterface
{

    /**
     * @param \Magento\Directory\Model\Country $country
     */
    public function __construct(
        \Magento\Directory\Model\Country $country
    ) {
        $this->country = $country;
    }

    /**
     * Get state type labels array.
     * @return array
     */
    public function toOptionArray()
    {
        $regionCollection = $this->country->loadByCode('IN')->getRegions();
        $regions = $regionCollection->loadData()->toOptionArray(false);
        return $regions;
     }
}
