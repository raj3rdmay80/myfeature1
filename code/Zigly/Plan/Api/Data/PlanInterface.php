<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Plan
 */
declare(strict_types=1);

namespace Zigly\Plan\Api\Data;

interface PlanInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const PLAN_CATEGORY = 'plan_category';
    const STATUS = 'status';
    const CUSTOMIZABLE_ACTIVITY = 'customizable_activity';
    const PRE_DEFINED = 'pre_defined';
    const APPLICABLE_CITIES = 'applicable_cities';
    const PLAN_ID = 'plan_id';
    const UPDATED_BY = 'updated_by';
    const DEFINED_ACTIVITY = 'defined_activity';
    const DEFINED_BREED = 'defined_breed';
    const CUSTOMIZABLE_BREED = 'customizable_breed';
    const DEFINED_DESCRIPTION = 'defined_description';
    const DESCRIPTION = 'description';
    const PLAN_TYPE = 'plan_type';
    const PLAN_NAME = 'plan_name';
    const CUSTOMIZABLE_DESCRIPTION = 'customizable_description';

    /**
     * Get plan_id
     * @return string|null
     */
    public function getPlanId();

    /**
     * Set plan_id
     * @param string $planId
     * @return \Zigly\Plan\Api\Data\PlanInterface
     */
    public function setPlanId($planId);

    /**
     * Get plan_name
     * @return string|null
     */
    public function getPlanName();

    /**
     * Set plan_name
     * @param string $planName
     * @return \Zigly\Plan\Api\Data\PlanInterface
     */
    public function setPlanName($planName);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Zigly\Plan\Api\Data\PlanExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Zigly\Plan\Api\Data\PlanExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Zigly\Plan\Api\Data\PlanExtensionInterface $extensionAttributes
    );

    /**
     * Get plan_type
     * @return string|null
     */
    public function getPlanType();

    /**
     * Set plan_type
     * @param string $planType
     * @return \Zigly\Plan\Api\Data\PlanInterface
     */
    public function setPlanType($planType);

    /**
     * Get description
     * @return string|null
     */
    public function getDescription();

    /**
     * Set description
     * @param string $description
     * @return \Zigly\Plan\Api\Data\PlanInterface
     */
    public function setDescription($description);

    /**
     * Get applicable_cities
     * @return string|null
     */
    public function getApplicableCities();

    /**
     * Set applicable_cities
     * @param string $applicableCities
     * @return \Zigly\Plan\Api\Data\PlanInterface
     */
    public function setApplicableCities($applicableCities);

    /**
     * Get plan_category
     * @return string|null
     */
    public function getPlanCategory();

    /**
     * Set plan_category
     * @param string $planCategory
     * @return \Zigly\Plan\Api\Data\PlanInterface
     */
    public function setPlanCategory($planCategory);

    /**
     * Get pre_defined
     * @return string|null
     */
    public function getPreDefined();

    /**
     * Set pre_defined
     * @param string $preDefined
     * @return \Zigly\Plan\Api\Data\PlanInterface
     */
    public function setPreDefined($preDefined);

    /**
     * Get defined_activity
     * @return string|null
     */
    public function getDefinedActivity();

    /**
     * Set defined_activity
     * @param string $definedActivity
     * @return \Zigly\Plan\Api\Data\PlanInterface
     */
    public function setDefinedActivity($definedActivity);

    /**
     * Get defined_description
     * @return string|null
     */
    public function getDefinedDescription();

    /**
     * Set defined_description
     * @param string $definedDescription
     * @return \Zigly\Plan\Api\Data\PlanInterface
     */
    public function setDefinedDescription($definedDescription);

    /**
     * Get defined_breed
     * @return string|null
     */
    public function getDefinedBreed();

    /**
     * Set defined_breed
     * @param string $definedBreed
     * @return \Zigly\Plan\Api\Data\PlanInterface
     */
    public function setDefinedBreed($definedBreed);

    /**
     * Get customizable_activity
     * @return string|null
     */
    public function getCustomizableActivity();

    /**
     * Set customizable_activity
     * @param string $customizableActivity
     * @return \Zigly\Plan\Api\Data\PlanInterface
     */
    public function setCustomizableActivity($customizableActivity);

    /**
     * Get customizable_description
     * @return string|null
     */
    public function getCustomizableDescription();

    /**
     * Set customizable_description
     * @param string $customizableDescription
     * @return \Zigly\Plan\Api\Data\PlanInterface
     */
    public function setCustomizableDescription($customizableDescription);

    /**
     * Get customizable_breed
     * @return string|null
     */
    public function getCustomizableBreed();

    /**
     * Set customizable_breed
     * @param string $customizableBreed
     * @return \Zigly\Plan\Api\Data\PlanInterface
     */
    public function setCustomizableBreed($customizableBreed);

    /**
     * Get status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     * @param string $status
     * @return \Zigly\Plan\Api\Data\PlanInterface
     */
    public function setStatus($status);

    /**
     * Get updated_by
     * @return string|null
     */
    public function getUpdatedBy();

    /**
     * Set updated_by
     * @param string $updatedBy
     * @return \Zigly\Plan\Api\Data\PlanInterface
     */
    public function setUpdatedBy($updatedBy);
}

