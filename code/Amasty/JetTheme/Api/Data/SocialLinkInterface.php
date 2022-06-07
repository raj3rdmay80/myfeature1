<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Api\Data;

interface SocialLinkInterface
{
    const TABLE_NAME = 'amasty_jet_social_network_link';
    const STORE_TABLE_NAME = 'amasty_jet_social_network_link_store';

    const ENTITY_ID = 'entity_id';
    const LINK = 'link';
    const DEFAULT_ICON = 'default_icon';
    const DEFAULT_ICON_CONTENT = 'default_icon_content';
    const ICON_FILE = 'icon_file';
    const STATUS = 'status';
    const SORT_ORDER = 'sort_order';
    const TITLE = 'title';
    const STORES = 'stores';

    const STORE_SOCIAL_ID_FIELD = 'social_link_id';
    const STORE_SOCIAL_STORE_ID_FIELD = 'store_id';

    /**
     * Get entity_id
     * @return int|string|null
     */
    public function getId();

    /**
     * Set entity_id
     * @param int|string $socialLinkId
     * @return SocialLinkInterface
     */
    public function setId($socialLinkId): SocialLinkInterface;

    /**
     * Get title
     * @return string|null
     */
    public function getTitle(): ?string;

    /**
     * Set title
     * @param string $title
     * @return SocialLinkInterface
     */
    public function setTitle(string $title): SocialLinkInterface;

    /**
     * Get link
     * @return string|null
     */
    public function getLink(): ?string;

    /**
     * Set link
     * @param string $link
     * @return SocialLinkInterface
     */
    public function setLink(string $link): SocialLinkInterface;

    /**
     * Get default icon
     * @return string|null
     */
    public function getDefaultIcon(): ?string;

    /**
     * Set default icon
     * @param string $icon
     * @return SocialLinkInterface
     */
    public function setDefaultIcon(string $icon): SocialLinkInterface;

    /**
     * Get default icon content
     * @return string|null
     */
    public function getDefaultIconContent(): ?string;

    /**
     * Set default icon content
     * @param string $icon
     * @return SocialLinkInterface
     */
    public function setDefaultIconContent(string $icon): SocialLinkInterface;

    /**
     * Get icon
     * @return string|null
     */
    public function getIcon(): ?string;

    /**
     * Set icon
     * @param string $icon
     * @return SocialLinkInterface
     */
    public function setIcon(string $icon): SocialLinkInterface;

    /**
     * Get status
     * @return int|null
     */
    public function getStatus(): ?int;

    /**
     * Set status
     * @param int $status
     * @return SocialLinkInterface
     */
    public function setStatus(int $status): SocialLinkInterface;

    /**
     * Get sort_order
     * @return int|null
     */
    public function getSortOrder(): ?int;

    /**
     * Set sort_order
     * @param int $sortOrder
     * @return SocialLinkInterface
     */
    public function setSortOrder(int $sortOrder): SocialLinkInterface;

    /**
     * Get Stores
     * @return array|null
     */
    public function getStores(): ?array;

    /**
     * Set Stores
     * @param array $stores
     * @return SocialLinkInterface
     */
    public function setStores(array $stores): SocialLinkInterface;
}
