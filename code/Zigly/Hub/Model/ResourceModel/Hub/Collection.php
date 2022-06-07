<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Hub
 */
declare(strict_types=1);

namespace Zigly\Hub\Model\ResourceModel\Hub;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'hub_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Zigly\Hub\Model\Hub::class,
            \Zigly\Hub\Model\ResourceModel\Hub::class
        );
    }
}

