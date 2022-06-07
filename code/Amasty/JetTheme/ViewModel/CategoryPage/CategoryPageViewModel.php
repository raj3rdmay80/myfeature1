<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\ViewModel\CategoryPage;

use Amasty\JetTheme\Model\ConfigProvider;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class CategoryPageViewModel implements ArgumentInterface
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
    public function getProductDisplayNameType()
    {
        return $this->configProvider->getProductDisplayNameType();
    }

    /**
     * @return string
     */
    public function getDisplayAddToCartButton()
    {
        return $this->configProvider->getDisplayAddToCartButton();
    }

    /**
     * @return string
     */
    public function getSwatchesType()
    {
        return $this->configProvider->getSwatchesType();
    }

    /**
     * @return string
     */
    public function getProductsPerLineMobile()
    {
        return $this->configProvider->getProductsPerLineMobile();
    }

    /**
     * @return string
     */
    public function getProductsPerLineDesktop()
    {
        return $this->configProvider->getProductsPerLineDesktop();
    }

    /**
     * @return string
     */
    public function getSwatchDisplayType()
    {
        return $this->configProvider->getSwatchDisplayType();
    }

    /**
     * @return string
     */
    public function getAddToWishlistPosition()
    {
        return $this->configProvider->getAddToWishlistPosition();
    }

    /**
     * @return string
     */
    public function getAddToComparePosition()
    {
        return $this->configProvider->getAddToComparePosition();
    }

    /**
     * @return array
     */
    public function getActualBlockWrappers(): array
    {
        $fullAddToComparePosition = $this->getAddToComparePosition();
        $shortAddToComparePosition = $this->getIconPosition($fullAddToComparePosition);

        $fullAddToWishlistPosition = $this->getAddToWishlistPosition();
        $shortAddToWishlistPosition = $this->getIconPosition($fullAddToWishlistPosition);

        $actualBlocksWrappers = [];
        $blockPositions = [
            $shortAddToComparePosition,
            $shortAddToWishlistPosition,
            'bottom-right',
            'bottom-left'
        ];

        foreach ($blockPositions as $position) {
            if ($position && !in_array($position, $actualBlocksWrappers, true)) {
                array_push($actualBlocksWrappers, $position);
            }
        }

        return $actualBlocksWrappers;
    }

    public function getIconPosition(string $iconPosition): string
    {
        return preg_replace('/-hover/', '', $iconPosition);
    }

    /**
     * @return bool
     */
    public function getShowShortDescription(): bool
    {
        return $this->configProvider->getShowShortDescription();
    }

    /**
     * @return bool
     */
    public function getShowProductReviews(): bool
    {
        return $this->configProvider->getShowProductReviews();
    }

    /**
     * @return string
     */
    public function getAlignAddToCart()
    {
        return $this->configProvider->getAlignAddToCart();
    }
}
