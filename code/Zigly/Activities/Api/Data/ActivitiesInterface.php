<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Activities
 */
declare(strict_types=1);

namespace Zigly\Activities\Api\Data;

interface ActivitiesInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const ACTIVITY_NAME = 'activity_name';
    const DESCRIPTION = 'description';
    const IS_ACTIVE = 'is_active';
    const ACTIVITIES_ID = 'activities_id';

    /**
     * Get activities_id
     * @return string|null
     */
    public function getActivitiesId();

    /**
     * Set activities_id
     * @param string $activitiesId
     * @return \Zigly\Activities\Api\Data\ActivitiesInterface
     */
    public function setActivitiesId($activitiesId);

    /**
     * Get activity_name
     * @return string|null
     */
    public function getActivityName();

    /**
     * Set activity_name
     * @param string $activityName
     * @return \Zigly\Activities\Api\Data\ActivitiesInterface
     */
    public function setActivityName($activityName);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Zigly\Activities\Api\Data\ActivitiesExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Zigly\Activities\Api\Data\ActivitiesExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Zigly\Activities\Api\Data\ActivitiesExtensionInterface $extensionAttributes
    );

    /**
     * Get description
     * @return string|null
     */
    public function getDescription();

    /**
     * Set description
     * @param string $description
     * @return \Zigly\Activities\Api\Data\ActivitiesInterface
     */
    public function setDescription($description);

    /**
     * Get is_active
     * @return string|null
     */
    public function getIsActive();

    /**
     * Set is_active
     * @param string $isActive
     * @return \Zigly\Activities\Api\Data\ActivitiesInterface
     */
    public function setIsActive($isActive);
}

