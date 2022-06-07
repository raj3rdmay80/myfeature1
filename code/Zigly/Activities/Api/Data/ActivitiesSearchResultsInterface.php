<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Activities
 */
declare(strict_types=1);

namespace Zigly\Activities\Api\Data;

interface ActivitiesSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Activities list.
     * @return \Zigly\Activities\Api\Data\ActivitiesInterface[]
     */
    public function getItems();

    /**
     * Set activity_name list.
     * @param \Zigly\Activities\Api\Data\ActivitiesInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

