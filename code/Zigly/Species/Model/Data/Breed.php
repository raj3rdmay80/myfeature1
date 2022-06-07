<?php
/**
 * Copyright © Zigly All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Zigly\Species\Model\Data;

use Zigly\Species\Api\Data\BreedInterface;

class Breed extends \Magento\Framework\Api\AbstractExtensibleObject implements BreedInterface
{

    /**
     * Get breed_id
     * @return string|null
     */
    public function getBreedId()
    {
        return $this->_get(self::BREED_ID);
    }

    /**
     * Set breed_id
     * @param string $breedId
     * @return \Zigly\Species\Api\Data\BreedInterface
     */
    public function setBreedId($breedId)
    {
        return $this->setData(self::BREED_ID, $breedId);
    }

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
     * @return \Zigly\Species\Api\Data\BreedInterface
     */
    public function setSpeciesId($speciesId)
    {
        return $this->setData(self::SPECIES_ID, $speciesId);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Zigly\Species\Api\Data\BreedExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Zigly\Species\Api\Data\BreedExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Zigly\Species\Api\Data\BreedExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
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
     * @return \Zigly\Species\Api\Data\BreedInterface
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
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
     * @return \Zigly\Species\Api\Data\BreedInterface
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * Get breed_type
     * @return string|null
     */
    public function getBreedType()
    {
        return $this->_get(self::BREED_TYPE);
    }

    /**
     * Set breed_type
     * @param string $breedType
     * @return \Zigly\Species\Api\Data\BreedInterface
     */
    public function setBreedType($breedType)
    {
        return $this->setData(self::BREED_TYPE, $breedType);
    }

    /**
     * Get image
     * @return string|null
     */
    public function getImage()
    {
        return $this->_get(self::IMAGE);
    }

    /**
     * Set image
     * @param string $image
     * @return \Zigly\Species\Api\Data\BreedInterface
     */
    public function setImage($image)
    {
        return $this->setData(self::IMAGE, $image);
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
     * @return \Zigly\Species\Api\Data\BreedInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt()
    {
        return $this->_get(self::CREATED_AT);
    }

    /**
     * Set created_at
     * @param string $createdAt
     * @return \Zigly\Species\Api\Data\BreedInterface
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Get updated_at
     * @return string|null
     */
    public function getUpdatedAt()
    {
        return $this->_get(self::UPDATED_AT);
    }

    /**
     * Set updated_at
     * @param string $updatedAt
     * @return \Zigly\Species\Api\Data\BreedInterface
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }
}

