<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomingService
 */
declare(strict_types=1);

namespace Zigly\GroomingService\Api\Data;

interface GroomingSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Grooming list.
     * @return \Zigly\GroomingService\Api\Data\GroomingInterface[]
     */
    public function getItems();

    /**
     * Set customer_id list.
     * @param \Zigly\GroomingService\Api\Data\GroomingInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

