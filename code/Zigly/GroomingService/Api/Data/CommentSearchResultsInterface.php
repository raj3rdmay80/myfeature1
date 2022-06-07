<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomingService
 */
declare(strict_types=1);

namespace Zigly\GroomingService\Api\Data;

interface CommentSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Comment list.
     * @return \Zigly\GroomingService\Api\Data\CommentInterface[]
     */
    public function getItems();

    /**
     * Set comment list.
     * @param \Zigly\GroomingService\Api\Data\CommentInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
