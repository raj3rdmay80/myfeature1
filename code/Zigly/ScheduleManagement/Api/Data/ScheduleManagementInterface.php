<?php
/**
 * Copyright © Zigly All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Zigly\ScheduleManagement\Api\Data;

interface ScheduleManagementInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const SCHEDULEMANAGEMENT_ID = 'schedulemanagement_id';
    const PROFESSIONAL_ID = 'professional_id';
    const SCHEDULE_DATE = 'schedule_date';
    const WORKING_MODE = 'working_mode';
    const SLOT = 'slot';
    const SLOT_START_TIME = 'slot_start_time';
    const BOOKING_ID = 'booking_id';
    const AVAILABILITY = 'availability';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * Get schedulemanagement_id.
     *
     * @return string|null
     */
    public function getSchedulemanagementId();

    /**
     * Set schedulemanagement_id.
     *
     * @param string $schedulemanagementId
     *
     * @return \Zigly\ScheduleManagement\Api\Data\ScheduleManagementInterface
     */
    public function setSchedulemanagementId($schedulemanagementId);

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
     * @return \Zigly\ScheduleManagement\Api\Data\ScheduleManagementInterface
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
     * @return \Zigly\ScheduleManagement\Api\Data\ScheduleManagementInterface
     */
    public function setScheduleDate($schedule_date);

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
     * @return \Zigly\ScheduleManagement\Api\Data\ScheduleManagementInterface
     */
    public function setWorkingMode($working_mode);

    /**
     * Get slot.
     *
     * @return string|null
     */
    public function getSlotStartTime();

    /**
     * Set slot_start_time.
     *
     * @param string $slot_start_time
     *
     * @return \Zigly\ScheduleManagement\Api\Data\ScheduleManagementInterface
     */
    public function setSlotStartTime($slot_start_time);

    /**
     * Get slot.
     *
     * @return string|null
     */
    public function getSlot();

    /**
     * Set slot.
     *
     * @param string $slot
     *
     * @return \Zigly\ScheduleManagement\Api\Data\ScheduleManagementInterface
     */
    public function setSlot($slot);

    /**
     * Get booking_id.
     *
     * @return string|null
     */
    public function getBookingId();

    /**
     * Set booking_id.
     *
     * @param string $booking_id
     *
     * @return \Zigly\ScheduleManagement\Api\Data\ScheduleManagementInterface
     */
    public function setBookingId($booking_id);

    /**
     * Get availability.
     *
     * @return string|null
     */
    public function getAvailability();

    /**
     * Set availability.
     *
     * @param string $availability
     *
     * @return \Zigly\ScheduleManagement\Api\Data\ScheduleManagementInterface
     */
    public function setAvailability($availability);

    /**
     * Get created_at.
     *
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at.
     *
     * @param string $created_at
     *
     * @return \Zigly\ScheduleManagement\Api\Data\ScheduleManagementInterface
     */
    public function setCreatedAt($created_at);

    /**
     * Get updated_at.
     *
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set updated_at.
     *
     * @param string $updated_at
     *
     * @return \Zigly\ScheduleManagement\Api\Data\ScheduleManagementInterface
     */
    public function setUpdatedAt($updated_at);

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Zigly\ScheduleManagement\Api\Data\ScheduleManagementExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @return $this
     */
    public function setExtensionAttributes(
        \Zigly\ScheduleManagement\Api\Data\ScheduleManagementExtensionInterface $extensionAttributes
    );
}
