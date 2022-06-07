<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Groomer
 */
declare(strict_types=1);

namespace Zigly\Groomer\Api\Data;

interface GroomerSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Groomer list.
     * @return \Zigly\Groomer\Api\Data\GroomerInterface[]
     */
    public function getItems();

    /**
     * Set profile_image list.
     * @param \Zigly\Groomer\Api\Data\GroomerInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

