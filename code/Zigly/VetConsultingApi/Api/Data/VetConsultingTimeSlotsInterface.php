<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_VetConsultingApi
 */
declare(strict_types=1);

namespace Zigly\VetConsultingApi\Api\Data;

interface VetConsultingTimeSlotsInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const START_HOURS = 'start_hours';
    const END_HOURS = 'end_hours';

    /**
     * Get start hours
     *
     * @return string|null
     */
    public function getStartHours();

   /**
     * Set start Hours
     * @param string $startHours
     * @return \Zigly\VetConsultingApi\Api\Data\VetConsultingTimeSlotsInterface
     */
    public function setStartHours($startHours);

    /**
     * Get End Hours
     *
     * @return string|null
     */
    public function getEndHours();

   /**
     * Set End Hourd
     * @param string $endHours
     * @return \Zigly\VetConsultingApi\Api\Data\VetConsultingTimeSlotsInterface
     */
    public function setEndHours($endHours);
}