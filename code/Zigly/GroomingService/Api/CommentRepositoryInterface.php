<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomingService
 */
declare(strict_types=1);

namespace Zigly\GroomingService\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface CommentRepositoryInterface
{

    /**
     * Save Comment
     * @param \Zigly\GroomingService\Api\Data\CommentInterface $comment
     * @return \Zigly\GroomingService\Api\Data\CommentInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Zigly\GroomingService\Api\Data\CommentInterface $comment
    );

    /**
     * Retrieve Comment
     * @param string $commentId
     * @return \Zigly\GroomingService\Api\Data\CommentInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($commentId);

    /**
     * Retrieve Comment matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Zigly\GroomingService\Api\Data\CommentSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Comment
     * @param \Zigly\GroomingService\Api\Data\CommentInterface $comment
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Zigly\GroomingService\Api\Data\CommentInterface $comment
    );

    /**
     * Delete Comment by ID
     * @param string $commentId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($commentId);
}
