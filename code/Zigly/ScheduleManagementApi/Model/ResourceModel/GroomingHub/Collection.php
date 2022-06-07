<?php
declare(strict_types=1);

namespace Zigly\ScheduleManagementApi\Model\ResourceModel\GroomingHub;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'hub_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \Zigly\ScheduleManagementApi\Model\GroomingHub::class,
            \Zigly\ScheduleManagementApi\Model\ResourceModel\GroomingHub::class
        );
    }
}

