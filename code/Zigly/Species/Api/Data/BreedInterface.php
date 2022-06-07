<?php
/**
 * Copyright © Zigly All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Zigly\Species\Api\Data;

interface BreedInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const BREED_TYPE = 'breed_type';
    const CREATED_AT = 'created_at';
    const SPECIES_ID = 'species_id';
    const IMAGE = 'image';
    const DESCRIPTION = 'description';
    const STATUS = 'status';
    const NAME = 'name';
    const UPDATED_AT = 'updated_at';
    const BREED_ID = 'breed_id';

    /**
     * Get breed_id
     * @return string|null
     */
    public function getBreedId();

    /**
     * Set breed_id
     * @param string $breedId
     * @return \Zigly\Species\Api\Data\BreedInterface
     */
    public function setBreedId($breedId);

    /**
     * Get species_id
     * @return string|null
     */
    public function getSpeciesId();

    /**
     * Set species_id
     * @param string $speciesId
     * @return \Zigly\Species\Api\Data\BreedInterface
     */
    public function setSpeciesId($speciesId);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Zigly\Species\Api\Data\BreedExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Zigly\Species\Api\Data\BreedExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Zigly\Species\Api\Data\BreedExtensionInterface $extensionAttributes
    );

    /**
     * Get name
     * @return string|null
     */
    public function getName();

    /**
     * Set name
     * @param string $name
     * @return \Zigly\Species\Api\Data\BreedInterface
     */
    public function setName($name);

    /**
     * Get description
     * @return string|null
     */
    public function getDescription();

    /**
     * Set description
     * @param string $description
     * @return \Zigly\Species\Api\Data\BreedInterface
     */
    public function setDescription($description);

    /**
     * Get breed_type
     * @return string|null
     */
    public function getBreedType();

    /**
     * Set breed_type
     * @param string $breedType
     * @return \Zigly\Species\Api\Data\BreedInterface
     */
    public function setBreedType($breedType);

    /**
     * Get image
     * @return string|null
     */
    public function getImage();

    /**
     * Set image
     * @param string $image
     * @return \Zigly\Species\Api\Data\BreedInterface
     */
    public function setImage($image);

    /**
     * Get status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     * @param string $status
     * @return \Zigly\Species\Api\Data\BreedInterface
     */
    public function setStatus($status);

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param string $createdAt
     * @return \Zigly\Species\Api\Data\BreedInterface
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
     * @return \Zigly\Species\Api\Data\BreedInterface
     */
    public function setUpdatedAt($updatedAt);
}

