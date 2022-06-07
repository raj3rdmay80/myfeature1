<?php
declare(strict_types=1);

namespace Zigly\ScheduleManagementApi\Model\ResourceModel\GroomingHubPincode;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'pincode_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \Zigly\ScheduleManagementApi\Model\GroomingHubPincode::class,
            \Zigly\ScheduleManagementApi\Model\ResourceModel\GroomingHubPincode::class
        );
    }
}

