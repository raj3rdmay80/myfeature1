<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Wallet
 */
declare(strict_types=1);

namespace Zigly\Wallet\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface WalletRepositoryInterface
{

    /**
     * Save Wallet
     * @param \Zigly\Wallet\Api\Data\WalletInterface $wallet
     * @return \Zigly\Wallet\Api\Data\WalletInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Zigly\Wallet\Api\Data\WalletInterface $wallet
    );

    /**
     * Retrieve Wallet
     * @param string $walletId
     * @return \Zigly\Wallet\Api\Data\WalletInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($walletId);

    /**
     * Retrieve Wallet matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Zigly\Wallet\Api\Data\WalletSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Wallet
     * @param \Zigly\Wallet\Api\Data\WalletInterface $wallet
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Zigly\Wallet\Api\Data\WalletInterface $wallet
    );

    /**
     * Delete Wallet by ID
     * @param string $walletId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($walletId);
}

