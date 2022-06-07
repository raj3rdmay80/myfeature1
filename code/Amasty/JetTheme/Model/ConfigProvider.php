<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Model;

use Amasty\Base\Model\ConfigProviderAbstract;
use Magento\Store\Model\ScopeInterface;

class ConfigProvider extends ConfigProviderAbstract
{
    /**
     * xpath prefix of module (section)
     */
    protected $pathPrefix = 'amasty_jettheme/';

    const GENERAL_BLOCK = 'general/';
    const ADDITIONAL_ELEMENTS = 'additional_elements/';
    const SOCIAL_LINKS = 'social_links/';
    const NEWSLETTER_BLOCK = 'newsletter/';
    const COLOR_SCHEME = 'color_scheme/';
    const FONTS = 'fonts/';
    const AJAX = 'ajax/';
    const CATEGORY_PAGE = 'category_page/';
    const CUSTOM_FOOTER = 'custom_footer/';
    const PRODUCT_PAGE = 'product_page/';
    const PAYMENT_LINKS = 'payment_links/';

    const STYLE_GUIDE_ACCESS = 'style_guide_access';
    const STICKY_ADD_TO_CART = 'sticky_add_to_cart';
    const SHOW_LINKS = 'show_links_block';
    const SHOW_PAYMENT_LINKS_FOOTER = 'show_payment_links_block_footer';
    const SHOW_PAYMENT_LINKS_MINICART = 'show_payment_links_block_minicart';
    const SHOW_PAYMENT_LINKS_CART_PAGE = 'show_payment_links_block_cart_page';
    const SHOW_PAYMENT_LINKS_PRODUCT_PAGE = 'show_payment_links_block_product_page';
    const SHOW_NEWSLETTER = 'show_newsletter_form';
    const HEADLINE_TEXT = 'headline_text';
    const ADDITIONAL_TEXT = 'additional_text';
    const PLACEHOLDER_EMAIL = 'placeholder_email';
    const BACKGROUND_IMAGE = 'background_image';
    const FORM_POSITION = 'form_position';
    const TEXT_POSITION = 'text_position';
    const IMAGE_POSITION = 'image_position';
    const ENABLE_CUSTOM_COLORS = 'enable_custom_colors';
    const ENABLE_GOOGLE_FONTS = 'enable_google_fonts';
    const GOOGLE_FONT = 'google_font';
    const ENABLE_ADD_TO_COMPARE = 'add_to_compare';
    const ENABLE_ADD_TO_WISHLIST = 'add_to_wishlist';
    const ENABLE_ADD_TO_CART = 'add_to_cart';
    const ENABLE_QUICK_VIEW = 'is_quickview_enabled';
    const ADD_TO_COMPARE_POSITION = 'add_to_compare_position';
    const IS_OPEN_MINICART = 'open_minicart';
    const IS_STICKY_MINICART_ENABLED = 'enable_sticky_minicart';
    const IS_LOGO_BLOCK_ENABLED = 'show_logo_block';
    const IS_SUBSCRIPTION_BLOCK_ENABLED = 'show_subscripion_block';
    const IS_SUBSCRIPTION_BLOCK_MOBILE_ENABLED = 'show_subscripion_block_mobile';
    const IS_CUSTOM_MINI_FOOTER_ENABLED = 'enable_custom_minifooter';
    const MINI_FOOTER_CMS_BLOCK = 'mini_footer_cms_block';
    const IS_STICKY_HEADER_ENABLED = 'enable_sticky_header';
    const IS_ALWAYS_DISPLAY_STICKY_HEADER = 'always_display_sticky_header';
    const DISPLAY_STICKY_HEADER_ON_SCROLL_DOWN = 'display_on_scroll_down';
    const HIDE_STICKY_HEADER_ON_SCROLL_DOWN = 'hide_on_scroll_down';
    const DISPLAY_STICKY_HEADER_ON_SCROLL_UP = 'display_on_scroll_up';
    const SWATCHES_TYPE = 'swatches_type';
    const SHOW_SKU = 'show_product_sku';
    const SHOW_STOCK_INFO = 'show_stock_info';
    const DISPLAY_PRODUCT_NAME = 'product_diplay_name';
    const ALIGN_ADD_TO_CART = 'align_add_to_cart';
    const SWATCHES_DISPLAY_TYPE = 'swatch_display_type';
    const ADD_TO_WISHLIST_POSITION = 'add_to_wishlist_position';
    const SHOW_SHORT_DESCRIPTION = 'show_short_description';
    const SHOW_PRODUCT_REVIEWS = 'show_product_reviews';
    const SHOW_ADD_TO_WISHLIST = 'show_add_to_wishlist';
    const SHOW_ADD_TO_COMPARE = 'show_add_to_compare';
    const THUMBNAILS_POSITION = 'image_gallery_thumbnails_position';
    const ADD_TO_CART_POSITION = 'add_to_cart_position';
    const DISPLAY_ADD_TO_CART_BUTTON = 'display_add_to_cart_button';
    const PRODUCT_PER_LINE_DESKTOP = 'products_per_line_desktop';
    const PRODUCT_PER_LINE_MOBILE = 'products_per_line_mobile';
    const IS_CUSTOM_LAYOUT = 'enable_custom_layout';
    const CUSTOM_LAYOUT_VALUE = 'custom_layout_value';
    const CUSTOM_CMS_BLOCK_ENABLED = 'enable_custom_cms_block';
    const CUSTOM_CMS_BLOCK = 'custom_cms_block';
    const ALIGN_PAYMENT_METHODS_BLOCK = 'align_footer_payment_links';

    /**
     * @return bool
     */
    public function isStyleGuideAccessEnabled()
    {
        return $this->isSetFlag(self::GENERAL_BLOCK . self::STYLE_GUIDE_ACCESS);
    }

    /**
     * @return bool
     */
    public function isStickAddToCartEnabled()
    {
        return $this->isSetFlag(self::ADDITIONAL_ELEMENTS . self::STICKY_ADD_TO_CART);
    }

    /**
     * @return bool
     */
    public function isShowLinksBlock()
    {
        return $this->isSetFlag(self::CUSTOM_FOOTER . self::SHOW_LINKS);
    }

    /**
     * @return bool
     */
    public function isShowPaymentLinksFooterBlock(): bool
    {
        return $this->isSetFlag(self::CUSTOM_FOOTER . self::SHOW_PAYMENT_LINKS_FOOTER);
    }

    /**
     * @return bool
     */
    public function isShowPaymentLinksMinicartBlock(): bool
    {
        return $this->isSetFlag(self::PAYMENT_LINKS . self::SHOW_PAYMENT_LINKS_MINICART);
    }

    /**
     * @return bool
     */
    public function isShowPaymentLinksCartPageBlock(): bool
    {
        return $this->isSetFlag(self::PAYMENT_LINKS . self::SHOW_PAYMENT_LINKS_CART_PAGE);
    }

    /**
     * @return bool
     */
    public function isShowPaymentLinksProductPageBlock(): bool
    {
        return $this->isSetFlag(self::PAYMENT_LINKS . self::SHOW_PAYMENT_LINKS_PRODUCT_PAGE);
    }

    /**
     * @return bool
     */
    public function isShowNewsletterForm()
    {
        return $this->isSetFlag(self::NEWSLETTER_BLOCK . self::SHOW_NEWSLETTER);
    }

    /**
     * @return string
     */
    public function getHeadlineText()
    {
        return $this->getValue(self::NEWSLETTER_BLOCK . self::HEADLINE_TEXT);
    }

    /**
     * @return string
     */
    public function getAdditionalText()
    {
        return $this->getValue(self::NEWSLETTER_BLOCK . self::ADDITIONAL_TEXT);
    }

    /**
     * @return string
     */
    public function getPlaceholderEmail()
    {
        return $this->getValue(self::NEWSLETTER_BLOCK . self::PLACEHOLDER_EMAIL);
    }

    /**
     * If you want whole path, then use:
     * $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . 'amasty/jet_theme/placeholder/'
     *
     * @return string
     */
    public function getBackgroundImage()
    {
        return $this->getValue(self::NEWSLETTER_BLOCK . self::BACKGROUND_IMAGE);
    }

    /**
     * @return string
     */
    public function getFormPosition()
    {
        return $this->getValue(self::NEWSLETTER_BLOCK . self::FORM_POSITION);
    }

    /**
     * @return string
     */
    public function getTextPosition()
    {
        return $this->getValue(self::NEWSLETTER_BLOCK . self::TEXT_POSITION);
    }

    /**
     * @return string
     */
    public function getImagePosition()
    {
        return $this->getValue(self::NEWSLETTER_BLOCK . self::IMAGE_POSITION);
    }

    /**
     * @param null|int $storeId
     * @return bool
     */
    public function isCustomColorsEnabled($storeId = null)
    {
        return $this->isSetFlag(self::COLOR_SCHEME . self::ENABLE_CUSTOM_COLORS, $storeId);
    }

    /**
     * @param string $setting
     *
     * @return string
     */
    public function getRgbSetting($setting)
    {
        return $this->getValue(self::COLOR_SCHEME . $setting);
    }

    /**
     * @param null|int $storeId
     * @return bool
     */
    public function isGoogleFontsEnabled($storeId = null)
    {
        return $this->isSetFlag(self::FONTS . self::ENABLE_GOOGLE_FONTS, $storeId);
    }

    /**
     * @param string $setting
     *
     * @return string
     */
    public function getFontSetting($setting)
    {
        return $this->getValue(self::FONTS . $setting);
    }

    /**
     *
     * @return string
     */
    public function getGoogleFontSetting()
    {
        return $this->getValue(self::FONTS . self::GOOGLE_FONT);
    }

    /**
     * @return bool
     */
    public function isEnabledAjaxAddToCompare()
    {
        return $this->isSetFlag(self::AJAX . self::ENABLE_ADD_TO_COMPARE);
    }

    /**
     * @return bool
     */
    public function isEnabledAjaxAddToWishlist()
    {
        return $this->isSetFlag(self::AJAX . self::ENABLE_ADD_TO_WISHLIST);
    }

    /**
     * @return bool
     */
    public function isEnabledAjaxAddToCart()
    {
        return $this->isSetFlag(self::AJAX . self::ENABLE_ADD_TO_CART);
    }

    /**
     * @return bool
     */
    public function isOpenMinicart(): bool
    {
        return $this->isSetFlag(self::ADDITIONAL_ELEMENTS . self::IS_OPEN_MINICART);
    }

    /**
     * @return bool
     */
    public function isStickyMinicartEnabled(): bool
    {
        return $this->isSetFlag(self::ADDITIONAL_ELEMENTS . self::IS_STICKY_MINICART_ENABLED);
    }

    /**
     * @return bool
     */
    public function isEnabledQuickView()
    {
        return $this->isSetFlag(self::CATEGORY_PAGE . self::ENABLE_QUICK_VIEW);
    }

    /**
     * @return bool
     */
    public function isLogoBlockEnabled(): bool
    {
        return $this->isSetFlag(self::CUSTOM_FOOTER . self::IS_LOGO_BLOCK_ENABLED);
    }

    /**
     * @return bool
     */
    public function isSubscriptionBlockEnabled(): bool
    {
        return $this->isSetFlag(self::CUSTOM_FOOTER . self::IS_SUBSCRIPTION_BLOCK_ENABLED);
    }

    /**
     * @return bool
     */
    public function isSubscriptionBlockMobileEnabled(): bool
    {
        return $this->isSetFlag(self::CUSTOM_FOOTER . self::IS_SUBSCRIPTION_BLOCK_MOBILE_ENABLED);
    }

    /**
     * @return bool
     */
    public function isCustomMiniFooterEnabled(): bool
    {
        return $this->isSetFlag(self::CUSTOM_FOOTER . self::IS_CUSTOM_MINI_FOOTER_ENABLED);
    }

    /**
     * @return int
     */
    public function getCustomMiniFooterCmsBlockId(): int
    {
        return (int)$this->getValue(self::CUSTOM_FOOTER . self::MINI_FOOTER_CMS_BLOCK);
    }

    /**
     * @return bool
     */
    public function isStickyHeaderEnabled(): bool
    {
        return $this->isSetFlag(self::ADDITIONAL_ELEMENTS . self::IS_STICKY_HEADER_ENABLED);
    }

    /**
     * @return bool
     */
    public function isAlwaysDisplayStickyHeader(): bool
    {
        return $this->isSetFlag(self::ADDITIONAL_ELEMENTS . self:: IS_ALWAYS_DISPLAY_STICKY_HEADER);
    }

    /**
     * @return bool
     */
    public function isDisplayOnScrollDown(): bool
    {
        return $this->isSetFlag(self::ADDITIONAL_ELEMENTS . self::DISPLAY_STICKY_HEADER_ON_SCROLL_DOWN);
    }

    /**
     * @return string
     */
    public function hideStickyHeaderOnScrollDownValue()
    {
        return $this->getValue(self::ADDITIONAL_ELEMENTS . self::HIDE_STICKY_HEADER_ON_SCROLL_DOWN);
    }

    /**
     * @return bool
     */
    public function isDisplayOnScrollUp(): bool
    {
        return $this->isSetFlag(self::ADDITIONAL_ELEMENTS . self::DISPLAY_STICKY_HEADER_ON_SCROLL_UP);
    }

    /**
     * @return string
     */
    public function getProductDisplayNameType()
    {
        return $this->getValue(self::CATEGORY_PAGE . self::DISPLAY_PRODUCT_NAME);
    }

    /**
     * @return string
     */
    public function getSwatchesType()
    {
        return $this->getValue(self::ADDITIONAL_ELEMENTS . self::SWATCHES_TYPE);
    }

    /**
     * @return bool
     */
    public function showProductSku(): bool
    {
        return $this->isSetFlag(self::PRODUCT_PAGE . self::SHOW_SKU);
    }

    /**
     * @return bool
     */
    public function showStockInfo(): bool
    {
        return $this->isSetFlag(self::PRODUCT_PAGE . self::SHOW_STOCK_INFO);
    }

    /**
     * @return string
     */
    public function getProductsPerLineMobile()
    {
        return $this->getValue(self::CATEGORY_PAGE . self::PRODUCT_PER_LINE_MOBILE);
    }

    /**
     * @return string
     */
    public function getProductsPerLineDesktop()
    {
        return $this->getValue(self::CATEGORY_PAGE . self::PRODUCT_PER_LINE_DESKTOP);
    }

    /**
     * @return string
     */
    public function getAddToCartPosition()
    {
        return $this->getValue(self::PRODUCT_PAGE . self::ADD_TO_CART_POSITION);
    }

    /**
     * @return string
     */
    public function getThumbnailPosition()
    {
        return $this->getValue(self::PRODUCT_PAGE . self::THUMBNAILS_POSITION);
    }

    /**
     * @return bool
     */
    public function showAddToWishlist(): bool
    {
        return $this->isSetFlag(self::PRODUCT_PAGE . self::SHOW_ADD_TO_WISHLIST);
    }

    /**
     * @return bool
     */
    public function getShowProductReviews(): bool
    {
        return $this->isSetFlag(self::CATEGORY_PAGE . self::SHOW_PRODUCT_REVIEWS);
    }

    /**
     * @return bool
     */
    public function getShowShortDescription(): bool
    {
        return $this->isSetFlag(self::CATEGORY_PAGE . self::SHOW_SHORT_DESCRIPTION);
    }

    /**
     * @return string
     */
    public function getSwatchDisplayType()
    {
        return $this->getValue(self::CATEGORY_PAGE . self::SWATCHES_DISPLAY_TYPE);
    }

    /**
     * @return string
     */
    public function getAddToComparePosition()
    {
        return $this->getValue(self::CATEGORY_PAGE . self::ADD_TO_COMPARE_POSITION);
    }

    /**
     * @return string
     */
    public function getAddToWishlistPosition()
    {
        return $this->getValue(self::CATEGORY_PAGE . self::ADD_TO_WISHLIST_POSITION);
    }

    /**
     * @return bool
     */
    public function showAddToCompare(): bool
    {
        return $this->isSetFlag(self::PRODUCT_PAGE . self::SHOW_ADD_TO_COMPARE);
    }

    /**
     * @return string
     */
    public function getDisplayAddToCartButton()
    {
        return $this->getValue(self::CATEGORY_PAGE . self::DISPLAY_ADD_TO_CART_BUTTON);
    }

    /**
     * @return string
     */
    public function getAlignAddToCart()
    {
        return $this->getValue(self::CATEGORY_PAGE . self::ALIGN_ADD_TO_CART);
    }

    /**
     * @return bool
     */
    public function isFooterCustomLayout(): bool
    {
        return $this->isSetFlag(self::CUSTOM_FOOTER . self::IS_CUSTOM_LAYOUT);
    }

    /**
     * @return string
     */
    public function getCustomLayoutValue(): string
    {
        return $this->getValue(self::CUSTOM_FOOTER . self::CUSTOM_LAYOUT_VALUE);
    }

    /**
     * @return bool
     */
    public function isCustomCmsBlockEnabled(): bool
    {
        return $this->isSetFlag(self::CUSTOM_FOOTER . self::CUSTOM_CMS_BLOCK_ENABLED);
    }

    /**
     * @return int
     */
    public function getCustomCmsBlockId(): int
    {
        return (int)$this->getValue(self::CUSTOM_FOOTER . self::CUSTOM_CMS_BLOCK);
    }

    /**
     * @return string
     */
    public function getAlignPaymentMethodsBlock(): string
    {
        return $this->getValue(self::CUSTOM_FOOTER . self::ALIGN_PAYMENT_METHODS_BLOCK);
    }

    /**
     * @param string $path
     * @param null $storeId
     * @param string $scope
     * @return mixed
     */
    public function getSettingValue($path, $storeId = null, $scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue($path, $scope, $storeId);
    }

    /**
     * @param string $path
     * @param null $storeId
     * @param string $scope
     * @return bool
     */
    public function isSetFlagSetting($path, $storeId = null, $scope = ScopeInterface::SCOPE_STORE): bool
    {
        return $this->scopeConfig->isSetFlag($path, $scope, $storeId);
    }
}
