<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_VetConsultingApi
 */
declare(strict_types=1);

namespace Zigly\VetConsultingApi\Api;

interface VetConsultingTimeSlotsRepositoryInterface
{
    /**
     * Get Timeslots in vetConsulting.
     * @return \Zigly\VetConsultingApi\Api\Data\VetConsultingTimeSlotsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getTimeSlotsVetConsulting();

}