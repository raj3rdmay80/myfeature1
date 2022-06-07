<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_ReviewTag
 */
declare(strict_types=1);

namespace Zigly\ReviewTag\Api\Data;

interface ReviewTagSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get ReviewTag list.
     * @return \Zigly\ReviewTag\Api\Data\ReviewTagInterface[]
     */
    public function getItems();

    /**
     * Set ReviewTag list.
     * @param \Zigly\ReviewTag\Api\Data\ReviewTagInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

