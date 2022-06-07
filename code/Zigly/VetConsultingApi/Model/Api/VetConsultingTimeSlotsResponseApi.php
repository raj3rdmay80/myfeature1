<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_VetConsultingApi
 */
declare(strict_types=1);

namespace Zigly\VetConsultingApi\Model\Api;

use Zigly\VetConsultingApi\Api\Data\VetConsultingTimeSlotsInterface;

class VetConsultingTimeSlotsResponceApi extends \Magento\Framework\Api\AbstractExtensibleObject implements VetConsultingTimeSlotsInterface
{

    /**
     * Get Start Hours
     * @return string|null
     */
    public function getStartHours()
    {
        return $this->_get(self::START_HOURS);
    }

    /**
     * Set Start Hours
     * @param string $startHours
     * @return string
     */
    public function setStartHours($startHours)
    {
        return $this->setData(self::START_HOURS, $startHours);
    }

     /**
     * Get End Hours
     *
     * @return string|null
     */
    public function getEndHours()
    {
        return $this->_get(self::END_HOURS);
    }

    /**
     * Set End Hours
     * @param string $endHours
     * @return string
     */
    public function setEndHours($endHours)
    {
        return $this->setData(self::END_HOURS, $endHours);
    }

}