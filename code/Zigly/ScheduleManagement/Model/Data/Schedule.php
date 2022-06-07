<?php
/**
 * Copyright © Zigly All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Zigly\ScheduleManagement\Model\Data;

use Zigly\ScheduleManagement\Api\Data\ScheduleInterface;

class Schedule extends \Magento\Framework\Model\AbstractExtensibleModel implements ScheduleInterface
{
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
     * @return \Zigly\ScheduleManagement\Api\Data\ScheduleInterface
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
     * @return \Zigly\ScheduleManagement\Api\Data\ScheduleInterface
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
     * @return \Zigly\ScheduleManagement\Api\Data\ScheduleInterface
     */
    public function setAvailability($availability)
    {
        return $this->setData(self::AVAILABILITY, $availability);
    }
}
