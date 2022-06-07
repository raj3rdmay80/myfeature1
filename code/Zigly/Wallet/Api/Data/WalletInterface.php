<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Wallet
 */
declare(strict_types=1);

namespace Zigly\Wallet\Api\Data;

interface WalletInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const PERFORMED_BY = 'performed_by';
    const CREATED_AT = 'created_at';
    const WALLET_ID = 'wallet_id';
    const COMMENT = 'comment';
    const FLAG = 'flag';
    const AMOUNT = 'amount';
    const UPDATED_AT = 'updated_at';

    /**
     * Get wallet_id
     * @return string|null
     */
    public function getWalletId();

    /**
     * Set wallet_id
     * @param string $walletId
     * @return \Zigly\Wallet\Api\Data\WalletInterface
     */
    public function setWalletId($walletId);

    /**
     * Get comment
     * @return string|null
     */
    public function getComment();

    /**
     * Set comment
     * @param string $comment
     * @return \Zigly\Wallet\Api\Data\WalletInterface
     */
    public function setComment($comment);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Zigly\Wallet\Api\Data\WalletExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Zigly\Wallet\Api\Data\WalletExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Zigly\Wallet\Api\Data\WalletExtensionInterface $extensionAttributes
    );

    /**
     * Get amount
     * @return string|null
     */
    public function getAmount();

    /**
     * Set amount
     * @param string $amount
     * @return \Zigly\Wallet\Api\Data\WalletInterface
     */
    public function setAmount($amount);

    /**
     * Get flag
     * @return string|null
     */
    public function getFlag();

    /**
     * Set flag
     * @param string $flag
     * @return \Zigly\Wallet\Api\Data\WalletInterface
     */
    public function setFlag($flag);

    /**
     * Get PerformedBy
     * @return string|null
     */
    public function getPerformedBy();

    /**
     * Set PerformedBy
     * @param string $PerformedBy
     * @return \Zigly\Wallet\Api\Data\WalletInterface
     */
    public function setPerformedBy($performedBy);

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param string $createdAt
     * @return \Zigly\Wallet\Api\Data\WalletInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Get updated_at
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set updated_at
     * @param string $updatedAt
     * @return \Zigly\Wallet\Api\Data\WalletInterface
     */
    public function setUpdatedAt($updatedAt);
}

