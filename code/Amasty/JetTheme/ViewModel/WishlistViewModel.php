<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Wishlist\Helper\Data;

class WishlistViewModel implements ArgumentInterface
{
    /**
     * Wishlist data
     *
     * @var Data
     */
    protected $wishlistData;

    public function __construct(
        Data $wishlistData
    ) {
        $this->wishlistData = $wishlistData;
    }

    /**
     * @return bool
     */
    public function isAllowWishlist(): bool
    {
        return $this->wishlistData->isAllow();
    }
}
