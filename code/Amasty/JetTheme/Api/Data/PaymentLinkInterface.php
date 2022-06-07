<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Api\Data;

interface PaymentLinkInterface
{
    const TABLE_NAME = 'amasty_jet_payment_link';
    const STORE_TABLE_NAME = 'amasty_jet_payment_link_store';

    const ENTITY_ID = 'entity_id';
    const DEFAULT_ICON = 'default_icon';
    const DEFAULT_ICON_CONTENT = 'default_icon_content';
    const ICON_FILE = 'icon_file';
    const STATUS = 'status';
    const SORT_ORDER = 'sort_order';
    const TITLE = 'title';
    const STORES = 'stores';

    const STORE_PAYMENT_ID_FIELD = 'payment_link_id';
    const STORE_PAYMENT_STORE_ID_FIELD = 'store_id';

    /**
     * Get entity_id
     * @return int|string|null
     */
    public function getId();

    /**
     * Set entity_id
     * @param int|string $paymentLinkId
     * @return PaymentLinkInterface
     */
    public function setId($paymentLinkId): PaymentLinkInterface;

    /**
     * Get title
     * @return string|null
     */
    public function getTitle(): ?string;

    /**
     * Set title
     * @param string $title
     * @return PaymentLinkInterface
     */
    public function setTitle(string $title): PaymentLinkInterface;

    /**
     * Get default icon
     * @return string|null
     */
    public function getDefaultIcon(): ?string;

    /**
     * Set default icon
     * @param string $icon
     * @return PaymentLinkInterface
     */
    public function setDefaultIcon(string $icon): PaymentLinkInterface;

    /**
     * Get default icon content
     * @return string|null
     */
    public function getDefaultIconContent(): ?string;

    /**
     * Set default icon content
     * @param string $icon
     * @return PaymentLinkInterface
     */
    public function setDefaultIconContent(string $icon): PaymentLinkInterface;

    /**
     * Get icon
     * @return string|null
     */
    public function getIcon(): ?string;

    /**
     * Set icon
     * @param string $icon
     * @return PaymentLinkInterface
     */
    public function setIcon(string $icon): PaymentLinkInterface;

    /**
     * Get status
     * @return int|null
     */
    public function getStatus(): ?int;

    /**
     * Set status
     * @param int $status
     * @return PaymentLinkInterface
     */
    public function setStatus(int $status): PaymentLinkInterface;

    /**
     * Get sort_order
     * @return int|null
     */
    public function getSortOrder(): ?int;

    /**
     * Set sort_order
     * @param int $sortOrder
     * @return PaymentLinkInterface
     */
    public function setSortOrder(int $sortOrder): PaymentLinkInterface;

    /**
     * Get Stores
     * @return array|null
     */
    public function getStores(): ?array;

    /**
     * Set Stores
     * @param array $stores
     * @return PaymentLinkInterface
     */
    public function setStores(array $stores): PaymentLinkInterface;
}
