<?php
/**
 * Copyright © Zigly All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Zigly\Species\Model\Data;

use Zigly\Species\Api\Data\SpeciesInterface;

class Species extends \Magento\Framework\Api\AbstractExtensibleObject implements SpeciesInterface
{

    /**
     * Get species_id
     * @return string|null
     */
    public function getSpeciesId()
    {
        return $this->_get(self::SPECIES_ID);
    }

    /**
     * Set species_id
     * @param string $speciesId
     * @return \Zigly\Species\Api\Data\SpeciesInterface
     */
    public function setSpeciesId($speciesId)
    {
        return $this->setData(self::SPECIES_ID, $speciesId);
    }

    /**
     * Get name
     * @return string|null
     */
    public function getName()
    {
        return $this->_get(self::NAME);
    }

    /**
     * Set name
     * @param string $name
     * @return \Zigly\Species\Api\Data\SpeciesInterface
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Zigly\Species\Api\Data\SpeciesExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Zigly\Species\Api\Data\SpeciesExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Zigly\Species\Api\Data\SpeciesExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
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
     * @return \Zigly\Species\Api\Data\SpeciesInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }
}

