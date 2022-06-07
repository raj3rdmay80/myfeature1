<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Activities
 */
declare(strict_types=1);

namespace Zigly\Activities\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface ActivitiesRepositoryInterface
{

    /**
     * Save Activities
     * @param \Zigly\Activities\Api\Data\ActivitiesInterface $activities
     * @return \Zigly\Activities\Api\Data\ActivitiesInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Zigly\Activities\Api\Data\ActivitiesInterface $activities
    );

    /**
     * Retrieve Activities
     * @param string $activitiesId
     * @return \Zigly\Activities\Api\Data\ActivitiesInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($activitiesId);

    /**
     * Retrieve Activities matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Zigly\Activities\Api\Data\ActivitiesSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Activities
     * @param \Zigly\Activities\Api\Data\ActivitiesInterface $activities
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Zigly\Activities\Api\Data\ActivitiesInterface $activities
    );

    /**
     * Delete Activities by ID
     * @param string $activitiesId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($activitiesId);
}

