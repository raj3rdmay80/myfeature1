<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Hub
 */
declare(strict_types=1);

namespace Zigly\Hub\Api\Data;

interface HubSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Hub list.
     * @return \Zigly\Hub\Api\Data\HubInterface[]
     */
    public function getItems();

    /**
     * Set location list.
     * @param \Zigly\Hub\Api\Data\HubInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

