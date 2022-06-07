<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Hub
 */
declare(strict_types=1);

namespace Zigly\Hub\Ui\Component\Listing\Column;

class State implements \Magento\Framework\Option\ArrayInterface
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