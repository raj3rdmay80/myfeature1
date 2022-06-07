<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Hub
 */
declare(strict_types=1);

namespace Zigly\Hub\Model\Data;

use Zigly\Hub\Api\Data\HubInterface;

class Hub extends \Magento\Framework\Api\AbstractExtensibleObject implements HubInterface
{

    /**
     * Get hub_id
     * @return string|null
     */
    public function getHubId()
    {
        return $this->_get(self::HUB_ID);
    }

    /**
     * Set hub_id
     * @param string $hubId
     * @return \Zigly\Hub\Api\Data\HubInterface
     */
    public function setHubId($hubId)
    {
        return $this->setData(self::HUB_ID, $hubId);
    }

    /**
     * Get location
     * @return string|null
     */
    public function getLocation()
    {
        return $this->_get(self::LOCATION);
    }

    /**
     * Set location
     * @param string $location
     * @return \Zigly\Hub\Api\Data\HubInterface
     */
    public function setLocation($location)
    {
        return $this->setData(self::LOCATION, $location);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Zigly\Hub\Api\Data\HubExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Zigly\Hub\Api\Data\HubExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Zigly\Hub\Api\Data\HubExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get city
     * @return string|null
     */
    public function getCity()
    {
        return $this->_get(self::CITY);
    }

    /**
     * Set city
     * @param string $city
     * @return \Zigly\Hub\Api\Data\HubInterface
     */
    public function setCity($city)
    {
        return $this->setData(self::CITY, $city);
    }

    /**
     * Get street_one
     * @return string|null
     */
    public function getStreetOne()
    {
        return $this->_get(self::STREET_ONE);
    }

    /**
     * Set street_one
     * @param string $streetOne
     * @return \Zigly\Hub\Api\Data\HubInterface
     */
    public function setStreetOne($streetOne)
    {
        return $this->setData(self::STREET_ONE, $streetOne);
    }

    /**
     * Get street_two
     * @return string|null
     */
    public function getStreetTwo()
    {
        return $this->_get(self::STREET_TWO);
    }

    /**
     * Set street_two
     * @param string $streetTwo
     * @return \Zigly\Hub\Api\Data\HubInterface
     */
    public function setStreetTwo($streetTwo)
    {
        return $this->setData(self::STREET_TWO, $streetTwo);
    }

    /**
     * Get state
     * @return string|null
     */
    public function getState()
    {
        return $this->_get(self::STATE);
    }

    /**
     * Set state
     * @param string $state
     * @return \Zigly\Hub\Api\Data\HubInterface
     */
    public function setState($state)
    {
        return $this->setData(self::STATE, $state);
    }

    /**
     * Get pincode
     * @return string|null
     */
    public function getPincode()
    {
        return $this->_get(self::PINCODE);
    }

    /**
     * Set pincode
     * @param string $pincode
     * @return \Zigly\Hub\Api\Data\HubInterface
     */
    public function setPincode($pincode)
    {
        return $this->setData(self::PINCODE, $pincode);
    }
}

