<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_VetConsultingApi
 */
declare(strict_types=1);

namespace Zigly\VetConsultingApi\Api\Data;

interface VetConsultingSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get VetConsulting list.
     * @return \Zigly\VetConsultingApi\Api\Data\VetConsultingSearchResultsInterface[]
    */
    public function getItems();

    /**
     * Set VetConsulting list..
     * @param \Zigly\VetConsultingApi\Api\Data\VetConsultingSearchResultsInterface[] $items
     * @return $this
    */
    public function setItems(array $items);

    /**
     * @return int
     */
    public function getCurrentPageCount();

    /**
     * @param int $currentPageCount
     * @return $this
     */
    public function setCurrentPageCount($currentPageCount);
}