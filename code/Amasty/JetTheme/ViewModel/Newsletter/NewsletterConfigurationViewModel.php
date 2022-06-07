<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\ViewModel\Newsletter;

use Amasty\JetTheme\Model\ConfigProvider;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;

class NewsletterConfigurationViewModel implements ArgumentInterface
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(ConfigProvider $configProvider, StoreManagerInterface $storeManager)
    {
        $this->configProvider = $configProvider;
        $this->storeManager = $storeManager;
    }

    /**
     * @return bool
     */
    public function isShowNewsletterForm(): bool
    {
        return $this->configProvider->isShowNewsletterForm();
    }

    /**
     * @return string
     */
    public function getHeadlineText()
    {
        return $this->configProvider->getHeadlineText();
    }

    /**
     * @return string
     */
    public function getAdditionalText()
    {
        return $this->configProvider->getAdditionalText();
    }

    /**
     * @return string
     */
    public function getPlaceholderEmail()
    {
        return $this->configProvider->getPlaceholderEmail();
    }

    /**
     * @return string
     */
    public function getFormPosition()
    {
        return $this->configProvider->getFormPosition();
    }

    /**
     * @return string
     */
    public function getTextPosition()
    {
        return $this->configProvider->getTextPosition();
    }

    /**
     * @return string
     */
    public function getImagePosition()
    {
        return $this->configProvider->getImagePosition();
    }

    /**
     * @return string|bool
     */
    public function getBackgroundImage()
    {
        $baseFolderUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        $baseFolderUrl .= 'amasty/jet_theme/placeholder/';
        $backgroundImage = $this->configProvider->getBackgroundImage();

        if ($backgroundImage) {
            return $baseFolderUrl . $backgroundImage;
        } else {
            return false;
        }
    }
}
