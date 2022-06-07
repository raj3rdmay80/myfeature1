<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Plan
 */
declare(strict_types=1);

namespace Zigly\Plan\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface PlanRepositoryInterface
{

    /**
     * Save Plan
     * @param \Zigly\Plan\Api\Data\PlanInterface $plan
     * @return \Zigly\Plan\Api\Data\PlanInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Zigly\Plan\Api\Data\PlanInterface $plan);

    /**
     * Retrieve Plan
     * @param string $planId
     * @return \Zigly\Plan\Api\Data\PlanInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($planId);

    /**
     * Retrieve Plan matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Zigly\Plan\Api\Data\PlanSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Plan
     * @param \Zigly\Plan\Api\Data\PlanInterface $plan
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Zigly\Plan\Api\Data\PlanInterface $plan);

    /**
     * Delete Plan by ID
     * @param string $planId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($planId);
}

