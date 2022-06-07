<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_VetConsultingApi
 */
declare(strict_types=1);

namespace Zigly\VetConsultingApi\Api\Data;

interface VetConsultingListingInterface
{

     /**
     * Get VetConsulting view list.
     * @return \Zigly\VetConsultingApi\Api\Data\VetConsultingListingInterface[]
     */
    public function getItems();

    /**
     * Set VetConsulting view list..
     * @param \Zigly\VetConsultingApi\Api\Data\VetConsultingListingInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}