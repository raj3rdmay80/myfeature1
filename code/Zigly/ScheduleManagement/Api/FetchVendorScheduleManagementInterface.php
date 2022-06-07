<?php
/**
 * Copyright © Zigly All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Zigly\ScheduleManagement\Api;

interface FetchVendorScheduleManagementInterface
{
    /**
     * GET for FetchVendorSchedule api.
     *
     * @param string $date
     * @param string $token
     *
     * @return Zigly\ScheduleManagement\Api\Data\ScheduleResponseInterface
     */
    public function getFetchVendorSchedule($date, $token);
}
