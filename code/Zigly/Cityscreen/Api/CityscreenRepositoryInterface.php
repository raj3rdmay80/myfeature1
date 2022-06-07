<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Cityscreen
 */
declare(strict_types=1);

namespace Zigly\Cityscreen\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface CityscreenRepositoryInterface
{

    /**
     * Save Cityscreen
     * @param \Zigly\Cityscreen\Api\Data\CityscreenInterface $cityscreen
     * @return \Zigly\Cityscreen\Api\Data\CityscreenInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Zigly\Cityscreen\Api\Data\CityscreenInterface $cityscreen
    );

    /**
     * Retrieve Cityscreen
     * @param string $cityscreenId
     * @return \Zigly\Cityscreen\Api\Data\CityscreenInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($cityscreenId);

    /**
     * Retrieve Cityscreen matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Zigly\Cityscreen\Api\Data\CityscreenSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Cityscreen
     * @param \Zigly\Cityscreen\Api\Data\CityscreenInterface $cityscreen
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Zigly\Cityscreen\Api\Data\CityscreenInterface $cityscreen
    );

    /**
     * Delete Cityscreen by ID
     * @param string $cityscreenId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($cityscreenId);
}

