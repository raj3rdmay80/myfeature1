<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Cityscreen
 */
declare(strict_types=1);

namespace Zigly\Cityscreen\Api\Data;

interface CityscreenInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const IS_ACTIVE = 'is_active';
    const CREATED_AT = 'created_at';
    const CITY = 'city';
    const TYPE = 'type';
    const UPDATED_AT = 'updated_at';
    const CITYSCREEN_ID = 'cityscreen_id';

    /**
     * Get cityscreen_id
     * @return string|null
     */
    public function getCityscreenId();

    /**
     * Set cityscreen_id
     * @param string $cityscreenId
     * @return \Zigly\Cityscreen\Api\Data\CityscreenInterface
     */
    public function setCityscreenId($cityscreenId);

    /**
     * Get type
     * @return string|null
     */
    public function getType();

    /**
     * Set type
     * @param string $type
     * @return \Zigly\Cityscreen\Api\Data\CityscreenInterface
     */
    public function setType($type);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Zigly\Cityscreen\Api\Data\CityscreenExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Zigly\Cityscreen\Api\Data\CityscreenExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Zigly\Cityscreen\Api\Data\CityscreenExtensionInterface $extensionAttributes
    );

    /**
     * Get city
     * @return string|null
     */
    public function getCity();

    /**
     * Set city
     * @param string $city
     * @return \Zigly\Cityscreen\Api\Data\CityscreenInterface
     */
    public function setCity($city);

    /**
     * Get is_active
     * @return string|null
     */
    public function getIsActive();

    /**
     * Set is_active
     * @param string $isActive
     * @return \Zigly\Cityscreen\Api\Data\CityscreenInterface
     */
    public function setIsActive($isActive);

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param string $createdAt
     * @return \Zigly\Cityscreen\Api\Data\CityscreenInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Get updated_at
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set updated_at
     * @param string $updatedAt
     * @return \Zigly\Cityscreen\Api\Data\CityscreenInterface
     */
    public function setUpdatedAt($updatedAt);
}

