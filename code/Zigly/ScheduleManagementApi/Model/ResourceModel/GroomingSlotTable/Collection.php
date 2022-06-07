<?php
declare(strict_types=1);

namespace Zigly\ScheduleManagementApi\Model\ResourceModel\GroomingSlotTable;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'slot_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \Zigly\ScheduleManagementApi\Model\GroomingSlotTable::class,
            \Zigly\ScheduleManagementApi\Model\ResourceModel\GroomingSlotTable::class
        );
    }
}

