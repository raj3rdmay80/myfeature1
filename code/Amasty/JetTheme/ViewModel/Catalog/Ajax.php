<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\ViewModel\Catalog;

use Amasty\JetTheme\Model\ConfigProvider;
use Magento\Customer\Model\Context as CustomerContext;
use Magento\Framework\App\Http\Context;
use Magento\Framework\Module\Manager;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Ajax implements ArgumentInterface
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @var Context
     */
    private $context;

    /**
     * @var UrlInterface
     */
    private $url;

    public function __construct(
        ConfigProvider $configProvider,
        Manager $moduleManager,
        Context  $context,
        UrlInterface $url
    ) {
        $this->configProvider = $configProvider;
        $this->moduleManager = $moduleManager;
        $this->context = $context;
        $this->url = $url;
    }

    /**
     * @return bool
     */
    public function isEnabledAjaxAddToCompare(): bool
    {
        return $this->configProvider->isEnabledAjaxAddToCompare();
    }

    /**
     * @return string
     */
    public function getAjaxAddToCompare(): string
    {
        return $this->url->getUrl('amasty_jettheme/ajax/addToCompare');
    }

    /**
     * @return bool
     */
    public function isEnabledAjaxAddToWishlist(): bool
    {
        return $this->configProvider->isEnabledAjaxAddToWishlist()
            && $this->context->getValue(CustomerContext::CONTEXT_AUTH);
    }

    /**
     * @return string
     */
    public function getAjaxAddToWishlist(): string
    {
        return $this->url->getUrl('amasty_jettheme/ajax/addToWishlist');
    }

    /**
     * @return bool
     */
    public function isEnabledQuickView(): bool
    {
        return $this->configProvider->isEnabledQuickView();
    }

    /**
     * @return string
     */
    public function getQuickViewUrl(): string
    {
        return $this->url->getUrl('amasty_jettheme/ajax/quickView');
    }

    /**
     * @return bool
     */
    public function isEnabledAjaxAddToCart(): bool
    {
        return !$this->isAmastyCartEnabled() && $this->configProvider->isEnabledAjaxAddToCart();
    }

    /**
     * @return string
     */
    public function getAjaxAddToCartUrl(): string
    {
        return $this->url->getUrl('amasty_jettheme/ajax/addToCart');
    }

    /**
     * @return bool
     */
    private function isAmastyCartEnabled(): bool
    {
        return $this->moduleManager->isEnabled('Amasty_Cart');
    }
}
