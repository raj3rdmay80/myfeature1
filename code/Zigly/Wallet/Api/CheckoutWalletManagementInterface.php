<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Wallet
 */
declare(strict_types=1);

namespace Zigly\Wallet\Api;

interface CheckoutWalletManagementInterface
{
    /**
     * Adds a Wallet points to a specified cart.
     *
     * @param int $cartId The cart ID.
     * @param float $points
     *
     * @return mixed
     */
    public function set($cartId, $points);

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param float $usedPoints
     *
     * @return mixed
     */
    public function collectCurrentTotals(\Magento\Quote\Model\Quote $quote, $usedPoints);

    /**
     * Deletes a Wallet points from a specified cart.
     *
     * @param int $cartId The cart ID.
     * @return bool
     */
    public function remove($cartId);
}
