<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Groomer
 */
declare(strict_types=1);

namespace Zigly\Groomer\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface GroomerRepositoryInterface
{

    /**
     * Save Groomer
     * @param \Zigly\Groomer\Api\Data\GroomerInterface $groomer
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Zigly\Groomer\Api\Data\GroomerInterface $groomer
    );

    /**
     * Retrieve Groomer
     * @param string $groomerId
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($groomerId);

    /**
     * Retrieve Groomer matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Zigly\Groomer\Api\Data\GroomerSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Groomer
     * @param \Zigly\Groomer\Api\Data\GroomerInterface $groomer
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Zigly\Groomer\Api\Data\GroomerInterface $groomer
    );

    /**
     * Delete Groomer by ID
     * @param string $groomerId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($groomerId);
}

