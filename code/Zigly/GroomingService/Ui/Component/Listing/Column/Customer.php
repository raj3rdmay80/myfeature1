<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomingService
 */
declare(strict_types=1);

namespace Zigly\GroomingService\Ui\Component\Listing\Column;

use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;

class Customer implements \Magento\Framework\Option\ArrayInterface
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
        $customerCollection = $this->collectionFactory->create();
        foreach ($customerCollection as $index => $value) {
            $res[] = ['value' => $value['entity_id'], 'label' => $value['firstname']];
        }
        return $res;
    }

}