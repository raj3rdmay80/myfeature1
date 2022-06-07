<?php
declare(strict_types=1);

namespace Zigly\ScheduleManagementApi\Api\Data;

interface GroomingSlotTableSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get GroomingSlotTable list.
     * @return \Zigly\ScheduleManagementApi\Api\Data\GroomingSlotTableInterface[]
     */
    public function getItems();

    /**
     * Set Slot_id list.
     * @param \Zigly\ScheduleManagementApi\Api\Data\GroomingSlotTableInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

