<?php
/**
 * Copyright © Zigly All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Zigly\ScheduleManagement\Api\Data;

interface ScheduleResponseInterface
{
    const PROFESSIONAL_ID = 'professional_id';
    const SCHEDULE_DATE = 'schedule_date';
    const WORKING_MODE = 'working_mode';
    const SCHEDULE = 'schedule';
    const APPLIED_TO = 'applied_to';
    const FOLLOWING_DAYS = 'following_days';

    /**
     * Get professional_id.
     *
     * @return string|null
     */
    public function getProfessionalId();

    /**
     * Set professional_id.
     *
     * @param string $professionalId
     *
     * @return \Zigly\ScheduleManagement\Api\Data\ScheduleResponseInterface
     */
    public function setProfessionalId($professionalId);

    /**
     * Get schedule_date.
     *
     * @return string|null
     */
    public function getScheduleDate();

    /**
     * Set schedule_date.
     *
     * @param string $schedule_date
     *
     * @return \Zigly\ScheduleManagement\Api\Data\ScheduleResponseInterface
     */
    public function setScheduleDate($schedule_date);

    /**
     * Set applied_to.
     *
     * @param string $applied_to
     *
     * @return \Zigly\ScheduleManagement\Api\Data\ScheduleResponseInterface
     */
    public function setAppliedTo($applied_to);

    /**
     * Get applied_to.
     *
     * @return string|null
     */
    public function getAppliedTo();

    /**
     * Set following_days.
     *
     * @param mixed $following_days
     *
     * @return \Zigly\ScheduleManagement\Api\Data\ScheduleResponseInterface
     */
    public function setFollowingDays($following_days);

    /**
     * Get following_days.
     *
     * @return mixed|null
     */
    public function getFollowingDays();

    /**
     * Get working_mode.
     *
     * @return string|null
     */
    public function getWorkingMode();

    /**
     * Set working_mode.
     *
     * @param string $working_mode
     *
     * @return \Zigly\ScheduleManagement\Api\Data\ScheduleResponseInterface
     */
    public function setWorkingMode($working_mode);

    /**
     * Get schedule.
     *
     * @return \Zigly\ScheduleManagement\Api\Data\ScheduleInterface[]
     */
    public function getSchedule();

    /**
     * Set schedule.
     *
     * @param \Zigly\ScheduleManagement\Api\Data\ScheduleInterface[] $schedule
     *
     * @return \Zigly\ScheduleManagement\Api\Data\ScheduleResponseInterface
     */
    public function setSchedule($schedule);
}
