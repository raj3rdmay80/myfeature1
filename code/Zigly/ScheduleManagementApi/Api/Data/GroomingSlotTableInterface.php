<?php
declare(strict_types=1);

namespace Zigly\ScheduleManagementApi\Api\Data;

interface GroomingSlotTableInterface
{

    const SLOT_ID = 'Slot_id';
    const GROOMINGSLOTTABLE_ID = 'Slot_id';
    const MESSAGE = 'message';
    const STATUS = 'status';

    /**
     * Get groomingslottable_id
     * @return string|null
     */
    public function getGroomingslottableId();

    /**
     * Set groomingslottable_id
     * @param string $groomingslottableId
     * @return \Zigly\ScheduleManagementApi\GroomingSlotTable\Api\Data\GroomingSlotTableInterface
     */
    public function setGroomingslottableId($groomingslottableId);

    /**
     * Get Slot_id
     * @return string|null
     */
    public function getSlotId();

    /**
     * Set Slot_id
     * @param string $slotId
     * @return \Zigly\ScheduleManagementApi\GroomingSlotTable\Api\Data\GroomingSlotTableInterface
     */
    public function setSlotId($slotId);



     /**
      * Get status
      *
      * @return string|null
      */
     public function getStatus();

    /**
      * Set status
      * @param string $status
      * @return \Zigly\Groomingapi\Api\Data\GetPetsInterface
      */
     public function setStatus($status);

     /**
      * Get message
      *
      * @return string|null
      */
     public function getMessage();

     /**
      * Set message
      * @param string $message
      * @return \Zigly\Groomingapi\Api\Data\GetPetsInterface
     */
     public function setMessage($message);
}

