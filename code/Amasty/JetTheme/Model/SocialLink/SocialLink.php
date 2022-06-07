<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Model\SocialLink;

use Amasty\JetTheme\Api\Data\SocialLinkInterface;
use Magento\Framework\Model\AbstractModel;

class SocialLink extends AbstractModel implements SocialLinkInterface
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\SocialLink::class);
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
     * @param int|string $socialLinkId
     * @return SocialLinkInterface
     */
    public function setId($entityId): SocialLinkInterface
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
     * @return SocialLinkInterface
     */
    public function setTitle(string $title): SocialLinkInterface
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * @return string|null
     */
    public function getLink(): ?string
    {
        return $this->getData(self::LINK);
    }

    /**
     * @param string $link
     * @return SocialLinkInterface
     */
    public function setLink(string $link): SocialLinkInterface
    {
        return $this->setData(self::LINK, $link);
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
     * @return SocialLinkInterface
     */
    public function setDefaultIcon(string $icon): SocialLinkInterface
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
     * @return SocialLinkInterface
     */
    public function setDefaultIconContent(string $icon): SocialLinkInterface
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
     * @return SocialLinkInterface
     */
    public function setIcon(string $icon): SocialLinkInterface
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
     * @return SocialLinkInterface
     */
    public function setStatus(int $status): SocialLinkInterface
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
     * @return SocialLinkInterface
     */
    public function setSortOrder(int $sortOrder): SocialLinkInterface
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
     * @return SocialLinkInterface
     */
    public function setStores(array $stores): SocialLinkInterface
    {
        return $this->setData(self::STORES, $stores);
    }
}
