<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Hub
 */
declare(strict_types=1);

namespace Zigly\Hub\Api\Data;

interface HubInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const STREET_TWO = 'street_two';
    const PINCODE = 'pincode';
    const CITY = 'city';
    const STATE = 'state';
    const LOCATION = 'location';
    const HUB_ID = 'hub_id';
    const STREET_ONE = 'street_one';

    /**
     * Get hub_id
     * @return string|null
     */
    public function getHubId();

    /**
     * Set hub_id
     * @param string $hubId
     * @return \Zigly\Hub\Api\Data\HubInterface
     */
    public function setHubId($hubId);

    /**
     * Get location
     * @return string|null
     */
    public function getLocation();

    /**
     * Set location
     * @param string $location
     * @return \Zigly\Hub\Api\Data\HubInterface
     */
    public function setLocation($location);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Zigly\Hub\Api\Data\HubExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Zigly\Hub\Api\Data\HubExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Zigly\Hub\Api\Data\HubExtensionInterface $extensionAttributes
    );

    /**
     * Get city
     * @return string|null
     */
    public function getCity();

    /**
     * Set city
     * @param string $city
     * @return \Zigly\Hub\Api\Data\HubInterface
     */
    public function setCity($city);

    /**
     * Get street_one
     * @return string|null
     */
    public function getStreetOne();

    /**
     * Set street_one
     * @param string $streetOne
     * @return \Zigly\Hub\Api\Data\HubInterface
     */
    public function setStreetOne($streetOne);

    /**
     * Get street_two
     * @return string|null
     */
    public function getStreetTwo();

    /**
     * Set street_two
     * @param string $streetTwo
     * @return \Zigly\Hub\Api\Data\HubInterface
     */
    public function setStreetTwo($streetTwo);

    /**
     * Get state
     * @return string|null
     */
    public function getState();

    /**
     * Set state
     * @param string $state
     * @return \Zigly\Hub\Api\Data\HubInterface
     */
    public function setState($state);

    /**
     * Get pincode
     * @return string|null
     */
    public function getPincode();

    /**
     * Set pincode
     * @param string $pincode
     * @return \Zigly\Hub\Api\Data\HubInterface
     */
    public function setPincode($pincode);
}

