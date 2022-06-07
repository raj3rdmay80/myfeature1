<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Managepets
 */
namespace Zigly\Managepets\Model\ResourceModel\Managepets;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'entity_id';
    protected $_eventPrefix = 'zigly_managepets_collection';
    protected $_eventObject = 'managepets_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Zigly\Managepets\Model\Managepets', 'Zigly\Managepets\Model\ResourceModel\Managepets');
    }

}
