<?php
/**
 * Copyright © Zigly All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Zigly\ScheduleManagement\Api;

interface SaveVendorScheduleManagementInterface
{
    /**
     * POST for SaveVendorSchedule api.
     *
     * @param string                                                      $token
     * @param Zigly\ScheduleManagement\Api\Data\ScheduleResponseInterface $schedule
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @return string
     */
    public function postSaveVendorSchedule($token, $schedule);
}
