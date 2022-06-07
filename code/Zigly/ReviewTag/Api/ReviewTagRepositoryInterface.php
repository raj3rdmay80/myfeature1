<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_ReviewTag
 */
declare(strict_types=1);

namespace Zigly\ReviewTag\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface ReviewTagRepositoryInterface
{

    /**
     * Save ReviewTag
     * @param \Zigly\ReviewTag\Api\Data\ReviewInterfaceTag $reviewTag
     * @return \Zigly\ReviewTag\Api\Data\ReviewTagInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Zigly\ReviewTag\Api\Data\ReviewTagInterface $reviewTag);

    /**
     * Retrieve ReviewTag
     * @param string $reviewTagId
     * @return \Zigly\ReviewTag\Api\Data\ReviewTagInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($reviewTagId);

    /**
     * Retrieve ReviewTag matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Zigly\ReviewTag\Api\Data\ReviewTagSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete ReviewTag
     * @param \Zigly\ReviewTag\Api\Data\ReviewTagInterface $reviewTag
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Zigly\ReviewTag\Api\Data\ReviewTagInterface $reviewTag);

    /**
     * Delete GroomerReview by ID
     * @param string $GroomerReviewId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($reviewTagId);
}

