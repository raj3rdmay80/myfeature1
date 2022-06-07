<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\ViewModel\ProductPage;

use Amasty\JetTheme\Model\ConfigProvider;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class ProductPageViewModel implements ArgumentInterface
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        ConfigProvider $configProvider
    ) {
        $this->configProvider = $configProvider;
    }

    /**
     * @return string
     */
    public function getSwatchesType()
    {
        return $this->configProvider->getSwatchesType();
    }

    /**
     * @return bool
     */
    public function showProductSku(): bool
    {
        return $this->configProvider->showProductSku();
    }

    /**
     * @return bool
     */
    public function showStockInfo(): bool
    {
        return $this->configProvider->showStockInfo();
    }

    /**
     * @return bool
     */
    public function showAddToWishlist(): bool
    {
        return $this->configProvider->showAddToWishlist();
    }

    /**
     * @return bool
     */
    public function showAddToCompare(): bool
    {
        return $this->configProvider->showAddToCompare();
    }

    /**
     * @return string
     */
    public function getThumbnailPosition()
    {
        return $this->configProvider->getThumbnailPosition();
    }

    /**
     * @return string
     */
    public function getAddToCartPosition()
    {
        return $this->configProvider->getAddToCartPosition();
    }
}
