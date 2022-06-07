<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Activities
 */
declare(strict_types=1);

namespace Zigly\Activities\Ui\Component\Listing\Column;

use Zigly\Species\Model\ResourceModel\Species\CollectionFactory;

class Species implements \Magento\Framework\Option\ArrayInterface
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
     * {@inheritdoc}
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