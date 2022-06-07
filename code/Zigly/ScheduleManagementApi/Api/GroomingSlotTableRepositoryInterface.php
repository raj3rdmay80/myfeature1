<?php
declare(strict_types=1);

namespace Zigly\ScheduleManagementApi\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface GroomingSlotTableRepositoryInterface
{

    /**
     * Save GroomingSlotTable
     * @param \Zigly\ScheduleManagementApi\Api\Data\GroomingSlotTableInterface $groomingSlotTable
     * @return \Zigly\ScheduleManagementApi\Api\Data\GroomingSlotTableInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Zigly\ScheduleManagementApi\Api\Data\GroomingSlotTableInterface $groomingSlotTable
    );

    /**
     * Retrieve GroomingSlotTable
     * @param string $groomingslottableId
     * @return \Zigly\ScheduleManagementApi\Api\Data\GroomingSlotTableInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($groomingslottableId);

    /**
     * Retrieve GroomingSlotTable matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Zigly\ScheduleManagementApi\Api\Data\GroomingSlotTableSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete GroomingSlotTable
     * @param \Zigly\ScheduleManagementApi\Api\Data\GroomingSlotTableInterface $groomingSlotTable
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Zigly\ScheduleManagementApi\Api\Data\GroomingSlotTableInterface $groomingSlotTable
    );

    /**
     * Delete GroomingSlotTable by ID
     * @param string $groomingslottableId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($groomingslottableId);

  
}

