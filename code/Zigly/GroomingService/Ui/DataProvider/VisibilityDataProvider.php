<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomingService
 */
declare(strict_types=1);

namespace Zigly\GroomingService\Ui\DataProvider;

use Zigly\GroomingService\Model\ResourceModel\Grooming\CollectionFactory;

class VisibilityDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{

    public function __construct(
        CollectionFactory $collectionFactory,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $collection = $collectionFactory->create();
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $meta,
            $data
        );
        $this->collection = $collectionFactory->create()->addFieldToFilter('visibility', ['eq' => 2]);
    }
}
