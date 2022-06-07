<?php
/**
 * Copyright © Zigly All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Zigly\Species\Api\Data;

interface SpeciesInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const STATUS = 'status';
    const NAME = 'name';
    const SPECIES_ID = 'species_id';

    /**
     * Get species_id
     * @return string|null
     */
    public function getSpeciesId();

    /**
     * Set species_id
     * @param string $speciesId
     * @return \Zigly\Species\Api\Data\SpeciesInterface
     */
    public function setSpeciesId($speciesId);

    /**
     * Get name
     * @return string|null
     */
    public function getName();

    /**
     * Set name
     * @param string $name
     * @return \Zigly\Species\Api\Data\SpeciesInterface
     */
    public function setName($name);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Zigly\Species\Api\Data\SpeciesExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Zigly\Species\Api\Data\SpeciesExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Zigly\Species\Api\Data\SpeciesExtensionInterface $extensionAttributes
    );

    /**
     * Get status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     * @param string $status
     * @return \Zigly\Species\Api\Data\SpeciesInterface
     */
    public function setStatus($status);
}

