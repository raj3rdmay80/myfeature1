<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Wallet
 */
declare(strict_types=1);

namespace Zigly\Wallet\Ui\DataProvider;

use Zigly\Wallet\Model\ResourceModel\Wallet\CollectionFactory;

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
        $this->collection = $collectionFactory->create()->addFieldToFilter('visibility', ['eq' => 1])->addFilterToMap('created_at', 'main_table.created_at')->addFilterToMap('customer_id', 'customer.email');
        $this->collection->getSelect()->joinLeft(['customer'=>'customer_entity'],"main_table.customer_id = customer.entity_id",['customer_id' => 'customer.email']);
    }
}
