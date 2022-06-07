<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Wallet
 */
declare(strict_types=1);

namespace Zigly\Wallet\Model\Data;

use Zigly\Wallet\Api\Data\WalletInterface;

class Wallet extends \Magento\Framework\Api\AbstractExtensibleObject implements WalletInterface
{

    /**
     * Get wallet_id
     * @return string|null
     */
    public function getWalletId()
    {
        return $this->_get(self::WALLET_ID);
    }

    /**
     * Set wallet_id
     * @param string $walletId
     * @return \Zigly\Wallet\Api\Data\WalletInterface
     */
    public function setWalletId($walletId)
    {
        return $this->setData(self::WALLET_ID, $walletId);
    }

    /**
     * Get comment
     * @return string|null
     */
    public function getComment()
    {
        return $this->_get(self::COMMENT);
    }

    /**
     * Set comment
     * @param string $comment
     * @return \Zigly\Wallet\Api\Data\WalletInterface
     */
    public function setComment($comment)
    {
        return $this->setData(self::COMMENT, $comment);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Zigly\Wallet\Api\Data\WalletExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Zigly\Wallet\Api\Data\WalletExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Zigly\Wallet\Api\Data\WalletExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get amount
     * @return string|null
     */
    public function getAmount()
    {
        return $this->_get(self::AMOUNT);
    }

    /**
     * Set amount
     * @param string $amount
     * @return \Zigly\Wallet\Api\Data\WalletInterface
     */
    public function setAmount($amount)
    {
        return $this->setData(self::AMOUNT, $amount);
    }

    /**
     * Get flag
     * @return string|null
     */
    public function getFlag()
    {
        return $this->_get(self::FLAG);
    }

    /**
     * Set flag
     * @param string $flag
     * @return \Zigly\Wallet\Api\Data\WalletInterface
     */
    public function setFlag($flag)
    {
        return $this->setData(self::FLAG, $flag);
    }

    /**
     * Get PerformedBy
     * @return string|null
     */
    public function getPerformedBy()
    {
        return $this->_get(self::PERFORMED_BY);
    }

    /**
     * Set by
     * @param string $by
     * @return \Zigly\Wallet\Api\Data\WalletInterface
     */
    public function setPerformedBy($performedBy)
    {
        return $this->setData(self::PERFORMED_BY, $performedBy);
    }

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt()
    {
        return $this->_get(self::CREATED_AT);
    }

    /**
     * Set created_at
     * @param string $createdAt
     * @return \Zigly\Wallet\Api\Data\WalletInterface
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Get updated_at
     * @return string|null
     */
    public function getUpdatedAt()
    {
        return $this->_get(self::UPDATED_AT);
    }

    /**
     * Set updated_at
     * @param string $updatedAt
     * @return \Zigly\Wallet\Api\Data\WalletInterface
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }
}

