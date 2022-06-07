<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomingService
 */
declare(strict_types=1);

namespace Zigly\GroomingService\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface GroomingRepositoryInterface
{

    /**
     * Save Grooming
     * @param \Zigly\GroomingService\Api\Data\GroomingInterface $grooming
     * @return \Zigly\GroomingService\Api\Data\GroomingInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Zigly\GroomingService\Api\Data\GroomingInterface $grooming
    );

    /**
     * Retrieve Grooming
     * @param string $entityId
     * @return \Zigly\GroomingService\Api\Data\GroomingInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($entityId);

    /**
     * Retrieve Grooming matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Zigly\GroomingService\Api\Data\GroomingSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Grooming
     * @param \Zigly\GroomingService\Api\Data\GroomingInterface $grooming
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Zigly\GroomingService\Api\Data\GroomingInterface $grooming
    );

    /**
     * Delete Grooming by ID
     * @param string $entityId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($entityId);
}

