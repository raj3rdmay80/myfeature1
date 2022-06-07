<?php
/**
 * Copyright © Zigly All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Zigly\Species\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface SpeciesRepositoryInterface
{

    /**
     * Save Species
     * @param \Zigly\Species\Api\Data\SpeciesInterface $species
     * @return \Zigly\Species\Api\Data\SpeciesInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Zigly\Species\Api\Data\SpeciesInterface $species
    );

    /**
     * Retrieve Species
     * @param string $speciesId
     * @return \Zigly\Species\Api\Data\SpeciesInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($speciesId);

    /**
     * Retrieve Species matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Zigly\Species\Api\Data\SpeciesSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Species
     * @param \Zigly\Species\Api\Data\SpeciesInterface $species
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Zigly\Species\Api\Data\SpeciesInterface $species
    );

    /**
     * Delete Species by ID
     * @param string $speciesId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($speciesId);
}

