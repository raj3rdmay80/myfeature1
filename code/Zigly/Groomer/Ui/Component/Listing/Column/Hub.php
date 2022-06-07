<?php
/**
* Copyright (C) 2021  Zigly
* @package   Zigly_Groomer
*/
namespace Zigly\Groomer\Ui\Component\Listing\Column;

use Zigly\Hub\Model\ResourceModel\Hub\CollectionFactory;

class Hub implements \Magento\Framework\Option\ArrayInterface
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
        $collection = $this->collectionFactory->create();
        foreach ($collection as $index => $value) {
            $res[] = ['value' => $value['hub_id'], 'label' => $value['location'].', '.$value['city']];
        }
        return $res;
    }
}