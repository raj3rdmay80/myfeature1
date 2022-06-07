<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Activities
 */
declare(strict_types=1);

namespace Zigly\Activities\Model\Data;

use Zigly\Activities\Api\Data\ActivitiesInterface;

class Activities extends \Magento\Framework\Api\AbstractExtensibleObject implements ActivitiesInterface
{

    /**
     * Get activities_id
     * @return string|null
     */
    public function getActivitiesId()
    {
        return $this->_get(self::ACTIVITIES_ID);
    }

    /**
     * Set activities_id
     * @param string $activitiesId
     * @return \Zigly\Activities\Api\Data\ActivitiesInterface
     */
    public function setActivitiesId($activitiesId)
    {
        return $this->setData(self::ACTIVITIES_ID, $activitiesId);
    }

    /**
     * Get activity_name
     * @return string|null
     */
    public function getActivityName()
    {
        return $this->_get(self::ACTIVITY_NAME);
    }

    /**
     * Set activity_name
     * @param string $activityName
     * @return \Zigly\Activities\Api\Data\ActivitiesInterface
     */
    public function setActivityName($activityName)
    {
        return $this->setData(self::ACTIVITY_NAME, $activityName);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Zigly\Activities\Api\Data\ActivitiesExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Zigly\Activities\Api\Data\ActivitiesExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Zigly\Activities\Api\Data\ActivitiesExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get description
     * @return string|null
     */
    public function getDescription()
    {
        return $this->_get(self::DESCRIPTION);
    }

    /**
     * Set description
     * @param string $description
     * @return \Zigly\Activities\Api\Data\ActivitiesInterface
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * Get is_active
     * @return string|null
     */
    public function getIsActive()
    {
        return $this->_get(self::IS_ACTIVE);
    }

    /**
     * Set is_active
     * @param string $isActive
     * @return \Zigly\Activities\Api\Data\ActivitiesInterface
     */
    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }
}

