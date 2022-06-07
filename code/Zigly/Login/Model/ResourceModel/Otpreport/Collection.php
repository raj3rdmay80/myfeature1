<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Login
 */
namespace Zigly\Login\Model\ResourceModel\Otpreport;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'entity_id';
    protected $_eventPrefix = 'zigly_otp_report_collection';
    protected $_eventObject = 'otp_report_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Zigly\Login\Model\Otpreport', 'Zigly\Login\Model\ResourceModel\Otpreport');
    }

}
