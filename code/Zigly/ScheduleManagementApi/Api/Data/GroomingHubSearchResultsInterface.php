<?php
declare(strict_types=1);

namespace Zigly\ScheduleManagementApi\Api\Data;

interface GroomingHubSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get GroomingHub list.
     * @return \Zigly\ScheduleManagementApi\Api\Data\GroomingHubInterface[]
     */
    public function getItems();

    /**
     * Set Hub_Id list.
     * @param \Zigly\ScheduleManagementApi\Api\Data\GroomingHubInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

