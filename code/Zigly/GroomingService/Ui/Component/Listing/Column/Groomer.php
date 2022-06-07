<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomingService
 */
declare(strict_types=1);

namespace Zigly\GroomingService\Ui\Component\Listing\Column;

use Zigly\Groomer\Model\ResourceModel\Groomer\CollectionFactory;

class Groomer implements \Magento\Framework\Option\ArrayInterface
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
        $customerCollection = $this->collectionFactory->create()->addFieldToFilter('professional_role', ['in' => [2,3]]);
        foreach ($customerCollection as $index => $value) {
            $res[] = ['value' => $value['groomer_id'], 'label' => $value['name']];
        }
        return $res;
    }

}