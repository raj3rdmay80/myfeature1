<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Groomer
 */
declare(strict_types=1);

namespace Zigly\Groomer\Model\ResourceModel\Groomer;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'groomer_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Zigly\Groomer\Model\Groomer::class,
            \Zigly\Groomer\Model\ResourceModel\Groomer::class
        );
    }
}

