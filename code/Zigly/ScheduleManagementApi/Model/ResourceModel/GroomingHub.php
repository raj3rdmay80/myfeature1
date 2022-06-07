<?php
declare(strict_types=1);

namespace Zigly\ScheduleManagementApi\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class GroomingHub extends AbstractDb
{

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('zigly_schedulemanagementapi_groominghub', 'hub_id');
    }
}

