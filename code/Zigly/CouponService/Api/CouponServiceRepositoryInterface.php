<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_CouponService
 */
declare(strict_types=1);

namespace Zigly\CouponService\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface CouponServiceRepositoryInterface
{

    /**
     * Save CouponService
     * @param \Zigly\CouponService\Api\Data\CouponServiceInterface $couponService
     * @return \Zigly\CouponService\Api\Data\CouponServiceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Zigly\CouponService\Api\Data\CouponServiceInterface $couponService
    );

    /**
     * Retrieve CouponService
     * @param string $couponserviceId
     * @return \Zigly\CouponService\Api\Data\CouponServiceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($couponserviceId);

    /**
     * Retrieve CouponService matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Zigly\CouponService\Api\Data\CouponServiceSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete CouponService
     * @param \Zigly\CouponService\Api\Data\CouponServiceInterface $couponService
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Zigly\CouponService\Api\Data\CouponServiceInterface $couponService
    );

    /**
     * Delete CouponService by ID
     * @param string $couponserviceId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($couponserviceId);
}

