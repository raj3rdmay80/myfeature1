<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Plan
 */
declare(strict_types=1);

namespace Zigly\Plan\Ui\Component\Listing\Column;

use Zigly\Cityscreen\Model\ResourceModel\Cityscreen\CollectionFactory;

class Cities implements \Magento\Framework\Option\ArrayInterface
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
        $cityCollection = $this->cityCollectionFactory->create();
        foreach ($cityCollection as $index => $value) {
            $res[] = ['value' => $value['cityscreen_id'], 'label' => $value['city']];
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