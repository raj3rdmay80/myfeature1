<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\DesignInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Theme\Model\ResourceModel\Theme\CollectionFactory;

class StoreThemeMapper
{
    const THEME_FOLDER= 'Amasty/';

    /**
     * @var array
     */
    private $storeThemeMap = [];

    /**
     * @var StoreRepositoryInterface
     */
    private $storeRepository;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var CollectionFactory
     */
    private $themeCollectionFactory;

    /**
     * @var DesignInterface
     */
    private $design;

    public function __construct(
        StoreRepositoryInterface $storeRepository,
        ScopeConfigInterface $scopeConfig,
        CollectionFactory $themeCollectionFactory,
        DesignInterface $design
    ) {
        $this->storeRepository = $storeRepository;
        $this->scopeConfig = $scopeConfig;
        $this->themeCollectionFactory = $themeCollectionFactory;
        $this->design = $design;
    }

    /**
     * @param string $themeFilePath
     * @return int|null
     */
    public function getStoreIdByThemeFilePath($themeFilePath): ?int
    {
        $pathParts = explode('/', $themeFilePath);
        $themeName = $pathParts[1] . '/' . $pathParts[2];
        if (isset($this->storeThemeMap[$themeName])) {
            return $this->storeThemeMap[$themeName];
        }

        $theme = $this->themeCollectionFactory->create()
            ->addFieldToFilter('theme_path', $themeName)
            ->getFirstItem();

        $themeId = $theme->getThemeId();
        foreach ($this->storeRepository->getList() as $store) {
            $storeTheme = $this->scopeConfig->getValue(
                DesignInterface::XML_PATH_THEME_ID,
                ScopeInterface::SCOPE_STORE,
                $store->getId()
            );
            if ($storeTheme == $themeId) {
                $this->storeThemeMap[$themeName] = (int)$store->getId();
                return $this->storeThemeMap[$themeName];
            }
        }

        return null;
    }

    /**
     * @return array
     */
    public function getStoresAppliedTheme(): array
    {
        $mainTheme = $this->themeCollectionFactory->create()
            ->addFieldToFilter('theme_path', 'Amasty/JetTheme')
            ->getFirstItem();
        $mainThemeId = $mainTheme->getThemeId();

        $themesIds = $this->themeCollectionFactory->create()
            ->addFieldToFilter('parent_id', $mainThemeId)
            ->getAllIds();
        $themesIds[] = $mainThemeId;
        $storesAppliedThemes = [];
        foreach ($this->storeRepository->getList() as $store) {
            $storeTheme = $this->scopeConfig->getValue(
                DesignInterface::XML_PATH_THEME_ID,
                ScopeInterface::SCOPE_STORE,
                $store->getId()
            );
            if (in_array($storeTheme, $themesIds)) {
                $storesAppliedThemes[] = $store->getName();
            }
        }

        return $storesAppliedThemes;
    }

    /**
     * @return bool
     */
    public function isCurrentThemeJetTheme(): bool
    {
        $themePath = $this->design->getDesignTheme()->getThemePath();
        if ($themePath) {
            return strpos($this->design->getDesignTheme()->getThemePath(), self::THEME_FOLDER) !== false;
        }

        return false;
    }
}
