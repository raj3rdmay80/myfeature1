<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Plan
 */
declare(strict_types=1);

namespace Zigly\Plan\Api\Data;

interface PlanSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Plan list.
     * @return \Zigly\Plan\Api\Data\PlanInterface[]
     */
    public function getItems();

    /**
     * Set plan_name list.
     * @param \Zigly\Plan\Api\Data\PlanInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

