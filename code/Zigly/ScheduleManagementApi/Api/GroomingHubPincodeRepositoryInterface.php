<?php
declare(strict_types=1);

namespace Zigly\ScheduleManagementApi\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface GroomingHubPincodeRepositoryInterface
{

    /**
     * Save GroomingHubPincode
     * @param \Zigly\ScheduleManagementApi\Api\Data\GroomingHubPincodeInterface $groomingHubPincode
     * @return \Zigly\ScheduleManagementApi\Api\Data\GroomingHubPincodeInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Zigly\ScheduleManagementApi\Api\Data\GroomingHubPincodeInterface $groomingHubPincode
    );

    /**
     * Retrieve GroomingHubPincode
     * @param string $groominghubpincodeId
     * @return \Zigly\ScheduleManagementApi\Api\Data\GroomingHubPincodeInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($groominghubpincodeId);

    /**
     * Retrieve GroomingHubPincode matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Zigly\ScheduleManagementApi\Api\Data\GroomingHubPincodeSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete GroomingHubPincode
     * @param \Zigly\ScheduleManagementApi\Api\Data\GroomingHubPincodeInterface $groomingHubPincode
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Zigly\ScheduleManagementApi\Api\Data\GroomingHubPincodeInterface $groomingHubPincode
    );

    /**
     * Delete GroomingHubPincode by ID
     * @param string $groominghubpincodeId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($groominghubpincodeId);
}

