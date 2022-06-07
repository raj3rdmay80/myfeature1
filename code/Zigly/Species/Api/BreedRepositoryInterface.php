<?php
/**
 * Copyright © Zigly All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Zigly\Species\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface BreedRepositoryInterface
{

    /**
     * Save Breed
     * @param \Zigly\Species\Api\Data\BreedInterface $breed
     * @return \Zigly\Species\Api\Data\BreedInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Zigly\Species\Api\Data\BreedInterface $breed
    );

    /**
     * Retrieve Breed
     * @param string $breedId
     * @return \Zigly\Species\Api\Data\BreedInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($breedId);

    /**
     * Retrieve Breed matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Zigly\Species\Api\Data\BreedSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Breed
     * @param \Zigly\Species\Api\Data\BreedInterface $breed
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Zigly\Species\Api\Data\BreedInterface $breed
    );

    /**
     * Delete Breed by ID
     * @param string $breedId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($breedId);
}

