<?php
/**
 * Copyright © Zigly All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Zigly\ScheduleManagement\Model\Data;

use Zigly\ScheduleManagement\Api\Data\ScheduleResponseInterface;

class ScheduleResponse extends \Magento\Framework\Model\AbstractExtensibleModel implements ScheduleResponseInterface
{
    /**
     * Get professional_id.
     *
     * @return string|null
     */
    public function getProfessionalId()
    {
        return $this->getData(self::PROFESSIONAL_ID);
    }

    /**
     * Set professional_id.
     *
     * @param string $professionalId
     *
     * @return \Zigly\ScheduleManagement\Api\Data\ScheduleResponseInterface
     */
    public function setProfessionalId($professionalId)
    {
        return $this->setData(self::PROFESSIONAL_ID, $professionalId);
    }

    /**
     * Get schedule_date.
     *
     * @return string|null
     */
    public function getScheduleDate()
    {
        return $this->getData(self::SCHEDULE_DATE);
    }

    /**
     * Set schedule_date.
     *
     * @param string $schedule_date
     *
     * @return \Zigly\ScheduleManagement\Api\Data\ScheduleResponseInterface
     */
    public function setScheduleDate($schedule_date)
    {
        return $this->setData(self::SCHEDULE_DATE, $schedule_date);
    }

    /**
     * Get applied_to.
     *
     * @return string|null
     */
    public function getAppliedTo()
    {
        return $this->getData(self::APPLIED_TO);
    }

    /**
     * Set applied_to.
     *
     * @param string $applied_to
     *
     * @return \Zigly\ScheduleManagement\Api\Data\ScheduleResponseInterface
     */
    public function setAppliedTo($applied_to)
    {
        return $this->setData(self::APPLIED_TO, $applied_to);
    }

    /**
     * Get following_days.
     *
     * @return mixed|null
     */
    public function getFollowingDays()
    {
        return $this->getData(self::FOLLOWING_DAYS);
    }

    /**
     * Set following_days.
     *
     * @param mixed $following_days
     *
     * @return \Zigly\ScheduleManagement\Api\Data\ScheduleResponseInterface
     */
    public function setFollowingDays($following_days)
    {
        return $this->setData(self::FOLLOWING_DAYS, $following_days);
    }

    /**
     * Get working_mode.
     *
     * @return string|null
     */
    public function getWorkingMode()
    {
        return $this->getData(self::WORKING_MODE);
    }

    /**
     * Set working_mode.
     *
     * @param string $working_mode
     *
     * @return \Zigly\ScheduleManagement\Api\Data\ScheduleResponseInterface
     */
    public function setWorkingMode($working_mode)
    {
        return $this->setData(self::WORKING_MODE, $working_mode);
    }

    /**
     * Get schedule.
     *
     * @return \Zigly\ScheduleManagement\Api\Data\ScheduleInterface[]
     */
    public function getSchedule()
    {
        return $this->getData(self::SCHEDULE);
    }

    /**
     * Set schedule.
     *
     * @param \Zigly\ScheduleManagement\Api\Data\ScheduleInterface[] $schedule
     *
     * @return \Zigly\ScheduleManagement\Api\Data\ScheduleResponseInterface
     */
    public function setSchedule($schedule)
    {
        return $this->setData(self::SCHEDULE, $schedule);
    }
}
