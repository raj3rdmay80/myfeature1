<?php
/**
 * Copyright © Zigly All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Zigly\ScheduleManagement\Model\Data;

use Zigly\ScheduleManagement\Api\Data\ScheduleManagementInterface;

class ScheduleManagement extends \Magento\Framework\Model\AbstractExtensibleModel implements ScheduleManagementInterface
{
    /**
     * Get schedulemanagement_id.
     *
     * @return string|null
     */
    public function getSchedulemanagementId()
    {
        return $this->getData(self::SCHEDULEMANAGEMENT_ID);
    }

    /**
     * Set schedulemanagement_id.
     *
     * @param string $schedulemanagementId
     *
     * @return \Zigly\ScheduleManagement\Api\Data\ScheduleManagementInterface
     */
    public function setSchedulemanagementId($schedulemanagementId)
    {
        return $this->setData(self::SCHEDULEMANAGEMENT_ID, $schedulemanagementId);
    }

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
     * @return \Zigly\ScheduleManagement\Api\Data\ScheduleManagementInterface
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
     * @return \Zigly\ScheduleManagement\Api\Data\ScheduleManagementInterface
     */
    public function setScheduleDate($schedule_date)
    {
        return $this->setData(self::SCHEDULE_DATE, $schedule_date);
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
     * @return \Zigly\ScheduleManagement\Api\Data\ScheduleManagementInterface
     */
    public function setWorkingMode($working_mode)
    {
        return $this->setData(self::WORKING_MODE, $working_mode);
    }

    /**
     * Get slot.
     *
     * @return string|null
     */
    public function getSlotStartTime()
    {
        return $this->getData(self::SLOT_START_TIME);
    }

    /**
     * Set slot_start_time.
     *
     * @param string $slot_start_time
     *
     * @return \Zigly\ScheduleManagement\Api\Data\ScheduleManagementInterface
     */
    public function setSlotStartTime($slot_start_time)
    {
        return $this->setData(self::SLOT_START_TIME, $slot_start_time);
    }

    /**
     * Get slot.
     *
     * @return string|null
     */
    public function getSlot()
    {
        return $this->getData(self::SLOT);
    }

    /**
     * Set slot.
     *
     * @param string $slot
     *
     * @return \Zigly\ScheduleManagement\Api\Data\ScheduleManagementInterface
     */
    public function setSlot($slot)
    {
        return $this->setData(self::SLOT, $slot);
    }

    /**
     * Get booking_id.
     *
     * @return string|null
     */
    public function getBookingId()
    {
        return $this->getData(self::BOOKING_ID);
    }

    /**
     * Set booking_id.
     *
     * @param string $booking_id
     *
     * @return \Zigly\ScheduleManagement\Api\Data\ScheduleManagementInterface
     */
    public function setBookingId($booking_id)
    {
        return $this->setData(self::BOOKING_ID, $booking_id);
    }

    /**
     * Get availability.
     *
     * @return string|null
     */
    public function getAvailability()
    {
        return $this->getData(self::AVAILABILITY);
    }

    /**
     * Set availability.
     *
     * @param string $availability
     *
     * @return \Zigly\ScheduleManagement\Api\Data\ScheduleManagementInterface
     */
    public function setAvailability($availability)
    {
        return $this->setData(self::AVAILABILITY, $availability);
    }

    /**
     * Get created_at.
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * Set created_at.
     *
     * @param string $created_at
     *
     * @return \Zigly\ScheduleManagement\Api\Data\ScheduleManagementInterface
     */
    public function setCreatedAt($created_at)
    {
        return $this->setData(self::CREATED_AT, $created_at);
    }

    /**
     * Get updated_at.
     *
     * @return string|null
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * Set updated_at.
     *
     * @param string $updated_at
     *
     * @return \Zigly\ScheduleManagement\Api\Data\ScheduleManagementInterface
     */
    public function setUpdatedAt($updated_at)
    {
        return $this->setData(self::UPDATED_AT, $updated_at);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Zigly\ScheduleManagement\Api\Data\ScheduleManagementExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->getDataExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     *
     * @return $this
     */
    public function setExtensionAttributes(
        \Zigly\ScheduleManagement\Api\Data\ScheduleManagementExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
