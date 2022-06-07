<?php
/**
 * Copyright © Zigly All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Zigly\ScheduleManagement\Api\Data;

interface ScheduleManagementSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get ScheduleManagement list.
     * @return \Zigly\ScheduleManagement\Api\Data\ScheduleManagementInterface[]
     */
    public function getItems();

    /**
     * Set professional_id list.
     * @param \Zigly\ScheduleManagement\Api\Data\ScheduleManagementInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

