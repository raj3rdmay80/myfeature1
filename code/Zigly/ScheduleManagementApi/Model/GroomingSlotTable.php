<?php
declare(strict_types=1);

namespace Zigly\ScheduleManagementApi\Model;

use Magento\Framework\Model\AbstractModel;
use Zigly\ScheduleManagementApi\Api\Data\GroomingSlotTableInterface;

class GroomingSlotTable extends AbstractModel implements GroomingSlotTableInterface
{

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Zigly\ScheduleManagementApi\Model\ResourceModel\GroomingSlotTable::class);
    }

    /**
     * @inheritDoc
     */
    public function getGroomingslottableId()
    {
        return $this->getData(self::GROOMINGSLOTTABLE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setGroomingslottableId($groomingslottableId)
    {
        return $this->setData(self::GROOMINGSLOTTABLE_ID, $groomingslottableId);
    }

    /**
     * @inheritDoc
     */
    public function getSlotId()
    {
        return $this->getData(self::SLOT_ID);
    }

    /**
     * @inheritDoc
     */
    public function setSlotId($slotId)
    {
        return $this->setData(self::SLOT_ID, $slotId);
    }

    /**
     * Get status
     * @return string|null
     */
    public function getStatus()
    {
        return $this->_get(self::STATUS);
    }

    /**
     * Set status
     * @param string $status
     * @return string
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

     /**
     * Get message
     *
     * @return string|null
     */
    public function getMessage()
    {
        return $this->_get(self::MESSAGE);
    }

    /**
     * Set message
     * @param string $message
     * @return string
     */
    public function setMessage($message)
    {
        return $this->setData(self::MESSAGE, $message);
    }
}

