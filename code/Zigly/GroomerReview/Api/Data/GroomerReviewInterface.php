<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomerReview
 */
declare(strict_types=1);

namespace Zigly\GroomerReview\Api\Data;

interface GroomerReviewInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{


    const GROOMERREVIEW_ID = 'groomerreview_id';
    const STAR_RAING = 'star_rating';
    const TAG_NAME = 'tag_name';
    const IS_ACTIVE = 'is_active';
    const GROOMER_ID = 'groomer_id';
    const CREATED_AT = 'created_at';
    const UPDATE_AT = 'updated_at';

    /**
     * Get reviewTag_id
     */
    public function getGroomerReviewId();

    /**
     * Set reviewTag_id
     */
    public function setGroomerReviewId($GroomerReviewId);

    /**
     * Get reviewTag_id
     */
    public function getStarRating();

    /**
     * Set reviewTag_id
     */
    public function setStarRating($starRating);

    /**
     * Get tag_name
     */
    public function getTagName();

    /**
     * Set tag_name
     */
    public function setTagName($tagName);

    /**
     * Get IsActive.
     *
     * @return varchar
     */
    public function getIsActive();

    /**
     * Set StartingPrice.
     */
    public function setIsActive($isActive);

    /**
     * Get Groomer Id.
     *
     * @return varchar
     */
    public function getGroomerId();

    /**
     * Set Groomer Id.
     */
    public function setGroomerId($groomerId);

    /**
     * Get UpdateTime.
     *
     * @return varchar
     */
    public function getUpdateAt();

    /**
     * Set UpdateTime.
     */
    public function setUpdateAt($updateAt);

    /**
     * Get CreatedAt.
     *
     * @return varchar
     */
    public function getCreatedAt();

    /**
     * Set CreatedAt.
     */
    public function setCreatedAt($createdAt);
}
