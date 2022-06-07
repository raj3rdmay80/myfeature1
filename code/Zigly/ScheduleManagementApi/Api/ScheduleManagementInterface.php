<?php
declare(strict_types=1);

namespace Zigly\ScheduleManagementApi\Api;

interface ScheduleManagementInterface
{

    /**
     * GET for schedule api
     * @param string $customerId
     * @param string $date
     * @param string $pincode
     * @return string
     */
    public function getSchedule($date , $pincode ,$customerId);
}

