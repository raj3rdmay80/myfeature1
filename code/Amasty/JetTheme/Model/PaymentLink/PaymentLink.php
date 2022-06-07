<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Model\PaymentLink;

use Amasty\JetTheme\Api\Data\PaymentLinkInterface;
use Magento\Framework\Model\AbstractModel;

class PaymentLink extends AbstractModel implements PaymentLinkInterface
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\PaymentLink::class);
    }

    /**
     * Get entity_id
     * @return string|null
     */
    public function getId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * @param int|string $paymentLinkId
     * @return PaymentLinkInterface
     */
    public function setId($entityId): PaymentLinkInterface
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->getData(self::TITLE);
    }

    /**
     * @param string $title
     * @return PaymentLinkInterface
     */
    public function setTitle(string $title): PaymentLinkInterface
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * @return string|null
     */
    public function getDefaultIcon(): ?string
    {
        return $this->getData(self::DEFAULT_ICON);
    }

    /**
     * @param string $icon
     * @return PaymentLinkInterface
     */
    public function setDefaultIcon(string $icon): PaymentLinkInterface
    {
        return $this->setData(self::DEFAULT_ICON, $icon);
    }

    /**
     * @return string|null
     */
    public function getDefaultIconContent(): ?string
    {
        return $this->getData(self::DEFAULT_ICON)
            // phpcs:ignore Magento2.Functions.DiscouragedFunction
            ? base64_decode($this->getData(self::DEFAULT_ICON_CONTENT))
            : null;
    }

    /**
     * @param string $icon
     * @return PaymentLinkInterface
     */
    public function setDefaultIconContent(string $icon): PaymentLinkInterface
    {
        return $this->setData(self::DEFAULT_ICON_CONTENT, $icon);
    }

    /**
     * @return string|null
     */
    public function getIcon(): ?string
    {
        return $this->getData(self::ICON_FILE);
    }

    /**
     * @param string $icon
     * @return PaymentLinkInterface
     */
    public function setIcon(string $icon): PaymentLinkInterface
    {
        return $this->setData(self::ICON_FILE, $icon);
    }

    /**
     * @return int|null
     */
    public function getStatus(): ?int
    {
        return (int)$this->getData(self::STATUS);
    }

    /**
     * @param int $status
     * @return PaymentLinkInterface
     */
    public function setStatus(int $status): PaymentLinkInterface
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @return int|null
     */
    public function getSortOrder(): ?int
    {
        return $this->getData(self::SORT_ORDER) !== null ? (int)$this->getData(self::SORT_ORDER) : null;
    }

    /**
     * @param int $sortOrder
     * @return PaymentLinkInterface
     */
    public function setSortOrder(int $sortOrder): PaymentLinkInterface
    {
        return $this->setData(self::SORT_ORDER, $sortOrder);
    }

    /**
     * Get Stores
     * @return array|null
     */
    public function getStores(): ?array
    {
        if ($this->getData(self::STORES) === null) {
            return null;
        }

        return is_array($this->getData(self::STORES))
            ? $this->getData(self::STORES)
            : explode(',', $this->getData(self::STORES));
    }

    /**
     * @param array $stores
     * @return PaymentLinkInterface
     */
    public function setStores(array $stores): PaymentLinkInterface
    {
        return $this->setData(self::STORES, $stores);
    }
}
