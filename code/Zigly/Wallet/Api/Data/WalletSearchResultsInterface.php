<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Wallet
 */
declare(strict_types=1);

namespace Zigly\Wallet\Api\Data;

interface WalletSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Wallet list.
     * @return \Zigly\Wallet\Api\Data\WalletInterface[]
     */
    public function getItems();

    /**
     * Set comment list.
     * @param \Zigly\Wallet\Api\Data\WalletInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

