<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Plan
 */
declare(strict_types=1);

namespace Zigly\Plan\Model\Data;

use Zigly\Plan\Api\Data\PlanInterface;

class Plan extends \Magento\Framework\Api\AbstractExtensibleObject implements PlanInterface
{

    /**
     * Get plan_id
     * @return string|null
     */
    public function getPlanId()
    {
        return $this->_get(self::PLAN_ID);
    }

    /**
     * Set plan_id
     * @param string $planId
     * @return \Zigly\Plan\Api\Data\PlanInterface
     */
    public function setPlanId($planId)
    {
        return $this->setData(self::PLAN_ID, $planId);
    }

    /**
     * Get plan_name
     * @return string|null
     */
    public function getPlanName()
    {
        return $this->_get(self::PLAN_NAME);
    }

    /**
     * Set plan_name
     * @param string $planName
     * @return \Zigly\Plan\Api\Data\PlanInterface
     */
    public function setPlanName($planName)
    {
        return $this->setData(self::PLAN_NAME, $planName);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Zigly\Plan\Api\Data\PlanExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Zigly\Plan\Api\Data\PlanExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Zigly\Plan\Api\Data\PlanExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get plan_type
     * @return string|null
     */
    public function getPlanType()
    {
        return $this->_get(self::PLAN_TYPE);
    }

    /**
     * Set plan_type
     * @param string $planType
     * @return \Zigly\Plan\Api\Data\PlanInterface
     */
    public function setPlanType($planType)
    {
        return $this->setData(self::PLAN_TYPE, $planType);
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
     * @return \Zigly\Plan\Api\Data\PlanInterface
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * Get applicable_cities
     * @return string|null
     */
    public function getApplicableCities()
    {
        return $this->_get(self::APPLICABLE_CITIES);
    }

    /**
     * Set applicable_cities
     * @param string $applicableCities
     * @return \Zigly\Plan\Api\Data\PlanInterface
     */
    public function setApplicableCities($applicableCities)
    {
        return $this->setData(self::APPLICABLE_CITIES, $applicableCities);
    }

    /**
     * Get plan_category
     * @return string|null
     */
    public function getPlanCategory()
    {
        return $this->_get(self::PLAN_CATEGORY);
    }

    /**
     * Set plan_category
     * @param string $planCategory
     * @return \Zigly\Plan\Api\Data\PlanInterface
     */
    public function setPlanCategory($planCategory)
    {
        return $this->setData(self::PLAN_CATEGORY, $planCategory);
    }

    /**
     * Get pre_defined
     * @return string|null
     */
    public function getPreDefined()
    {
        return $this->_get(self::PRE_DEFINED);
    }

    /**
     * Set pre_defined
     * @param string $preDefined
     * @return \Zigly\Plan\Api\Data\PlanInterface
     */
    public function setPreDefined($preDefined)
    {
        return $this->setData(self::PRE_DEFINED, $preDefined);
    }

    /**
     * Get defined_activity
     * @return string|null
     */
    public function getDefinedActivity()
    {
        return $this->_get(self::DEFINED_ACTIVITY);
    }

    /**
     * Set defined_activity
     * @param string $definedActivity
     * @return \Zigly\Plan\Api\Data\PlanInterface
     */
    public function setDefinedActivity($definedActivity)
    {
        return $this->setData(self::DEFINED_ACTIVITY, $definedActivity);
    }

    /**
     * Get defined_description
     * @return string|null
     */
    public function getDefinedDescription()
    {
        return $this->_get(self::DEFINED_DESCRIPTION);
    }

    /**
     * Set defined_description
     * @param string $definedDescription
     * @return \Zigly\Plan\Api\Data\PlanInterface
     */
    public function setDefinedDescription($definedDescription)
    {
        return $this->setData(self::DEFINED_DESCRIPTION, $definedDescription);
    }

    /**
     * Get defined_breed
     * @return string|null
     */
    public function getDefinedBreed()
    {
        return $this->_get(self::DEFINED_BREED);
    }

    /**
     * Set defined_breed
     * @param string $definedBreed
     * @return \Zigly\Plan\Api\Data\PlanInterface
     */
    public function setDefinedBreed($definedBreed)
    {
        return $this->setData(self::DEFINED_BREED, $definedBreed);
    }

    /**
     * Get customizable_activity
     * @return string|null
     */
    public function getCustomizableActivity()
    {
        return $this->_get(self::CUSTOMIZABLE_ACTIVITY);
    }

    /**
     * Set customizable_activity
     * @param string $customizableActivity
     * @return \Zigly\Plan\Api\Data\PlanInterface
     */
    public function setCustomizableActivity($customizableActivity)
    {
        return $this->setData(self::CUSTOMIZABLE_ACTIVITY, $customizableActivity);
    }

    /**
     * Get customizable_description
     * @return string|null
     */
    public function getCustomizableDescription()
    {
        return $this->_get(self::CUSTOMIZABLE_DESCRIPTION);
    }

    /**
     * Set customizable_description
     * @param string $customizableDescription
     * @return \Zigly\Plan\Api\Data\PlanInterface
     */
    public function setCustomizableDescription($customizableDescription)
    {
        return $this->setData(self::CUSTOMIZABLE_DESCRIPTION, $customizableDescription);
    }

    /**
     * Get customizable_breed
     * @return string|null
     */
    public function getCustomizableBreed()
    {
        return $this->_get(self::CUSTOMIZABLE_BREED);
    }

    /**
     * Set customizable_breed
     * @param string $customizableBreed
     * @return \Zigly\Plan\Api\Data\PlanInterface
     */
    public function setCustomizableBreed($customizableBreed)
    {
        return $this->setData(self::CUSTOMIZABLE_BREED, $customizableBreed);
    }

    /**
     * Get status
     * @return string|null
     */
    public function getStatus()
    {
        return $this->_get(self::STATUS);
    }

    /**
     * Set status
     * @param string $status
     * @return \Zigly\Plan\Api\Data\PlanInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Get updated_by
     * @return string|null
     */
    public function getUpdatedBy()
    {
        return $this->_get(self::UPDATED_BY);
    }

    /**
     * Set updated_by
     * @param string $updatedBy
     * @return \Zigly\Plan\Api\Data\PlanInterface
     */
    public function setUpdatedBy($updatedBy)
    {
        return $this->setData(self::UPDATED_BY, $updatedBy);
    }
}

