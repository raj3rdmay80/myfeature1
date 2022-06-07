<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Api;

use Amasty\JetTheme\Api\Data\SocialLinkInterface;
use Amasty\JetTheme\Api\Data\SocialLinkSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

interface SocialLinkRepositoryInterface
{

    /**
     * Save SocialLink
     * @param SocialLinkInterface $socialLink
     * @return SocialLinkInterface
     * @throws LocalizedException
     */
    public function save(SocialLinkInterface $socialLink): SocialLinkInterface;

    /**
     * Retrieve SocialLink
     * @param int $socialLinkId
     * @return SocialLinkInterface
     * @throws NoSuchEntityException
     */
    public function get(int $socialLinkId): SocialLinkInterface;

    /**
     * Retrieve SocialLink matching the specified criteria.
     * @param SearchCriteriaInterface $searchCriteria
     * @return SocialLinkSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete SocialLink
     * @param SocialLinkInterface $socialLink
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(SocialLinkInterface $socialLink): bool;

    /**
     * Delete SocialLink by ID
     * @param int $socialLinkId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById(int $socialLinkId): bool;
}
