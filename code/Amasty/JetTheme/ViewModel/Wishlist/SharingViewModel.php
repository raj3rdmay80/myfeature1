<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\ViewModel\Wishlist;

use Magento\Framework\Session\Generic;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Wishlist\Model\Config;

class SharingViewModel implements ArgumentInterface
{
    /**
     * Entered Data cache
     *
     * @var array|null
     */
    private $enteredData = null;

    /**
     * Wishlist configuration
     *
     * @var Config
     */
    private $wishlistConfig;

    /**
     * @var Generic
     */
    private $wishlistSession;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    public function __construct(
        Config $wishlistConfig,
        Generic $wishlistSession,
        UrlInterface $urlBuilder
    ) {
        $this->wishlistConfig = $wishlistConfig;
        $this->wishlistSession = $wishlistSession;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Retrieve Send Form Action URL
     *
     * @return string
     */
    public function getSendUrl(): string
    {
        return $this->urlBuilder->getUrl('wishlist/index/send');
    }

    /**
     * Retrieve Entered Data by key
     *
     * @param string $key
     * @return string|null
     */
    public function getEnteredData(string $key): ?string
    {
        if ($this->enteredData === null) {
            $this->enteredData = $this->wishlistSession->getData('sharing_form', true);
        }

        if (!$this->enteredData || !isset($this->enteredData[$key])) {
            return null;
        } else {
            return $this->enteredData[$key];
        }
    }

    /**
     * Retrieve back button url
     *
     * @return string
     */
    public function getBackUrl(): string
    {
        return $this->urlBuilder->getUrl('wishlist');
    }
}
