<?php
/**
 * Copyright © Zigly All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Zigly\ScheduleManagement\Api\Data;

interface ScheduleInterface
{
    const SLOT = 'slot';
    const BOOKING_ID = 'booking_id';
    const AVAILABILITY = 'availability';

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
     * @return \Zigly\ScheduleManagement\Api\Data\ScheduleInterface
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
     * @return \Zigly\ScheduleManagement\Api\Data\ScheduleInterface
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
     * @return \Zigly\ScheduleManagement\Api\Data\ScheduleInterface
     */
    public function setAvailability($availability);
}
