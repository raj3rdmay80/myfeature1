<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbyLite
 */


namespace Amasty\ShopbyLite\Helper;

use Magento\Catalog\Model\Layer;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Amasty\ShopbyLite\Model\Di\Wrapper;

class Data extends AbstractHelper
{
    const CONFIG_GENERAL_MULTISELECT = 'general/multiselect_filter_list';
    const CONFIG_GENERAL_AJAX = 'general/ajax_enabled';
    const CONFIG_GENERAL_SCROLL_UP = 'general/ajax_scroll_up';
    const CONFIG_GENERAL_SLIDER = 'general/enable_price_slider';
    const CONFIG_GENERAL_SLIDER_STEP = 'general/price_slider_step';
    const CONFIG_GENERAL_OWERFLOW_SCROLL = 'general/enable_overflow_scroll';
    const AMBRAND_INDEX_INDEX = 'ambrand_index_index';

    const DEFAULT_SLIDER_STEP_VALUE = 1.0;

    /**
     * @var  Layer
     */
    protected $layer;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Amasty\ShopbyLite\Model\Request
     */
    protected $shopbyRequest;

    /**
     * @var  \Amasty\ShopbyLite\Model\Layer\FilterList
     */
    protected $filterList;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var Layer\Resolver
     */
    private $layerResolver;

    /**
     * @var Wrapper
     */
    private $baseHelper;

    public function __construct(
        Context $context,
        Layer\Resolver $layerResolver,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry,
        Wrapper $baseHelper
    ) {
        parent::__construct($context);
        $this->layerResolver = $layerResolver;
        $this->storeManager = $storeManager;
        $this->registry = $registry;
        $this->baseHelper = $baseHelper;
    }

    /**
     * @param $path
     * @param int $storeId
     * @return mixed
     */
    public function getModuleConfig($path, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            'amshopby/' . $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param $path
     * @param null $storeId
     *
     * @return bool
     */
    public function isSetFlag($path, $storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            'amshopby/' . $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @return bool
     */
    public function isAjaxEnabled()
    {
        return $this->isSetFlag(self::CONFIG_GENERAL_AJAX);
    }

    /**
     * @return bool
     */
    public function isScrollUp()
    {
        return $this->isSetFlag(self::CONFIG_GENERAL_SCROLL_UP);
    }

    /**
     * @param \Magento\Catalog\Model\Layer\Filter\Item[] $activeFilters
     * @return string
     */
    public function getAjaxCleanUrl($activeFilters)
    {
        $filterState = [];

        foreach ($activeFilters as $item) {
            $filterState[$item->getFilter()->getRequestVar()] = $item->getFilter()->getCleanValue();
        }

        $filterState['p'] = null;

        $params['_current'] = true;
        $params['_use_rewrite'] = true;
        $params['_query'] = $filterState;
        $params['_escape'] = true;

        return str_replace('&amp;', '&', $this->_urlBuilder->getUrl('*/*/*', $params));
    }

    /**
     * @return mixed
     */
    public function getCurrentUrl()
    {
        $params['_current'] = true;
        $params['_use_rewrite'] = true;
        $params['_query'] = [
            '_' => null,
            'shopbyAjax' => null,
            \Amasty\ShopbyLite\Plugin\Framework\App\FrontController::SHOPBY_EXTRA_PARAM => null
        ];

        return str_replace('&amp;', '&', $this->_urlBuilder->getUrl('*/*/*', $params));
    }

    /**
     * @return \Magento\Catalog\Model\Category
     */
    public function getCurrentCategory()
    {
        return $this->getLayer()->getCurrentCategory();
    }

    /**
     * @return Layer
     */
    public function getLayer()
    {
        if (!$this->layer) {
            $this->layer = $this->layerResolver->get();
        }
        return $this->layer;
    }

    /**
     * @param $attributeCode
     *
     * @return bool
     */
    public function isMultiSelectAllowed($attributeCode)
    {
        $filterCodes = explode(',', $this->getModuleConfig(self::CONFIG_GENERAL_MULTISELECT));

        return in_array($attributeCode, $filterCodes);
    }

    /**
     * @return bool
     */
    public function isSliderAllowed()
    {
        return $this->isSetFlag(self::CONFIG_GENERAL_SLIDER);
    }

    /**
     * @return float
     */
    public function getSliderStepValue(): float
    {
        $sliderStepValue = $this->getModuleConfig(self::CONFIG_GENERAL_SLIDER_STEP);

        return $sliderStepValue ? (float)$sliderStepValue : self::DEFAULT_SLIDER_STEP_VALUE;
    }

    /**
     * @return int
     */
    public function getScrollOverflowValue()
    {
        return $this->getModuleConfig(self::CONFIG_GENERAL_OWERFLOW_SCROLL);
    }

    /**
     * @return \Magento\Framework\App\RequestInterface
     */
    public function getRequest()
    {
        return parent::_getRequest();
    }

    /**
     * @return bool
     */
    public function isBrandPage()
    {
        return $this->getRequest()->getFullActionName() == self::AMBRAND_INDEX_INDEX;
    }

    /**
     * @return string
     */
    public function getBrandAttributeCode()
    {
        return $this->baseHelper->getBrandAttributeCode();
    }
}
