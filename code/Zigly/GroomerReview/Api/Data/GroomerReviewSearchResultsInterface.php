<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomerReview
 */
declare(strict_types=1);

namespace Zigly\GroomerReview\Api\Data;

interface GroomerReviewSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get GroomerReview list.
     * @return \Zigly\GroomerReview\Api\Data\GroomerReviewInterface[]
     */
    public function getItems();

    /**
     * Set GroomerReview list.
     * @param \Zigly\GroomerReview\Api\Data\GroomerReviewInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

