<?php
declare(strict_types=1);

namespace Zigly\ScheduleManagementApi\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface GroomingHubRepositoryInterface
{

    /**
     * Save GroomingHub
     * @param \Zigly\ScheduleManagementApi\Api\Data\GroomingHubInterface $groomingHub
     * @return \Zigly\ScheduleManagementApi\Api\Data\GroomingHubInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Zigly\ScheduleManagementApi\Api\Data\GroomingHubInterface $groomingHub
    );

    /**
     * Retrieve GroomingHub
     * @param string $groominghubId
     * @return \Zigly\ScheduleManagementApi\Api\Data\GroomingHubInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($groominghubId);

    /**
     * Retrieve GroomingHub matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Zigly\ScheduleManagementApi\Api\Data\GroomingHubSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete GroomingHub
     * @param \Zigly\ScheduleManagementApi\Api\Data\GroomingHubInterface $groomingHub
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Zigly\ScheduleManagementApi\Api\Data\GroomingHubInterface $groomingHub
    );

    /**
     * Delete GroomingHub by ID
     * @param string $groominghubId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($groominghubId);
}

