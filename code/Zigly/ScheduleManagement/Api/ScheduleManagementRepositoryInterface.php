<?php
/**
 * Copyright © Zigly All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Zigly\ScheduleManagement\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface ScheduleManagementRepositoryInterface
{

    /**
     * Save ScheduleManagement
     * @param \Zigly\ScheduleManagement\Api\Data\ScheduleManagementInterface $scheduleManagement
     * @return \Zigly\ScheduleManagement\Api\Data\ScheduleManagementInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Zigly\ScheduleManagement\Api\Data\ScheduleManagementInterface $scheduleManagement
    );

    /**
     * Retrieve ScheduleManagement
     * @param string $schedulemanagementId
     * @return \Zigly\ScheduleManagement\Api\Data\ScheduleManagementInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($schedulemanagementId);

    /**
     * Retrieve ScheduleManagement matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Zigly\ScheduleManagement\Api\Data\ScheduleManagementSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete ScheduleManagement
     * @param \Zigly\ScheduleManagement\Api\Data\ScheduleManagementInterface $scheduleManagement
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Zigly\ScheduleManagement\Api\Data\ScheduleManagementInterface $scheduleManagement
    );

    /**
     * Delete ScheduleManagement by ID
     * @param string $schedulemanagementId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($schedulemanagementId);
}

