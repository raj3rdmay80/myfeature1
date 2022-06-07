<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Api;

use Amasty\JetTheme\Api\Data\PaymentLinkInterface;
use Amasty\JetTheme\Api\Data\PaymentLinkSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

interface PaymentLinkRepositoryInterface
{

    /**
     * @param PaymentLinkInterface $paymentLink
     * @return PaymentLinkInterface
     * @throws LocalizedException
     */
    public function save(PaymentLinkInterface $paymentLink): PaymentLinkInterface;

    /**
     * @param int $paymentLinkId
     * @return PaymentLinkInterface
     * @throws NoSuchEntityException
     */
    public function get(int $paymentLinkId): PaymentLinkInterface;

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return PaymentLinkSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * @param PaymentLinkInterface $paymentLink
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(PaymentLinkInterface $paymentLink): bool;

    /**
     * @param int $paymentLinkId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById(int $paymentLinkId): bool;
}
