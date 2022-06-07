<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Cityscreen
 */
declare(strict_types=1);

namespace Zigly\Cityscreen\Api\Data;

interface CityscreenSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Cityscreen list.
     * @return \Zigly\Cityscreen\Api\Data\CityscreenInterface[]
     */
    public function getItems();

    /**
     * Set type list.
     * @param \Zigly\Cityscreen\Api\Data\CityscreenInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

