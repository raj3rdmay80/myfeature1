<?php
/**
 * Copyright © Zigly All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Zigly\ScheduleManagement\Model\ResourceModel\ScheduleManagement;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'schedulemanagement_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            "\Zigly\ScheduleManagement\Model\ScheduleManagement",
            "\Zigly\ScheduleManagement\Model\ResourceModel\ScheduleManagement"
        );
    }
}

