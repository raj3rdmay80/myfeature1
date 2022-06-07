<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomerReview
 */
declare(strict_types=1);

namespace Zigly\GroomerReview\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface GroomerReviewRepositoryInterface
{

    /**
     * Save GroomerReview
     * @param \Zigly\GroomerReview\Api\Data\GroomerReviewInterface $groomerReview
     * @return \Zigly\GroomerReview\Api\Data\GroomerReviewInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Zigly\GroomerReview\Api\Data\GroomerReviewInterface $groomerReview);

    /**
     * Retrieve GroomerReview
     * @param string $groomerReview
     * @return \Zigly\GroomerReview\Api\Data\GroomerReviewInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($groomerReviewId);

    /**
     * Retrieve GroomerReview matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Zigly\GroomerReview\Api\Data\GroomerReviewSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete GroomerReview
     * @param \Zigly\GroomerReview\Api\Data\GroomerReviewInterface $groomerReview
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Zigly\GroomerReview\Api\Data\GroomerReviewInterface $groomerReview);

    /**
     * Delete GroomerReview by ID
     * @param string $GroomerReviewId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($groomerReviewId);
}

