<?php
declare(strict_types=1);

namespace Zigly\ScheduleManagementApi\Api\Data;

interface GroomingHubPincodeSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get GroomingHubPincode list.
     * @return \Zigly\ScheduleManagementApi\Api\Data\GroomingHubPincodeInterface[]
     */
    public function getItems();

    /**
     * Set PincodeId list.
     * @param \Zigly\ScheduleManagementApi\Api\Data\GroomingHubPincodeInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

