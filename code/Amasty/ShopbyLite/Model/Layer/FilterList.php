<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbyLite
 */


namespace Amasty\ShopbyLite\Model\Layer;

class FilterList extends \Magento\Catalog\Model\Layer\FilterList
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * @var bool
     */
    private $filtersLoaded  = false;

    /**
     * @var  \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Amasty\ShopbyLite\Model\Request
     */
    private $shopbyRequest;

    /**
     * @var \Amasty\ShopbyLite\Helper\Data
     */
    private $data;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Catalog\Model\Layer\FilterableAttributeListInterface $filterableAttributes,
        \Amasty\Base\Model\MagentoVersion $magentoVersion,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Registry $registry,
        \Amasty\ShopbyLite\Model\Request $shopbyRequest,
        \Amasty\ShopbyLite\Helper\Data $data,
        array $filters = []
    ) {
        $this->request = $request;
        $this->registry = $registry;
        $this->shopbyRequest = $shopbyRequest;
        $this->data = $data;

        $version = str_replace(['-develop', 'dev-', '-beta'], '', $magentoVersion->get());

        if (version_compare($version, '2.4.0', '>=')) {
            $params = [
                $objectManager,
                $filterableAttributes,
                $objectManager->create(\Magento\Catalog\Model\Config\LayerCategoryConfig::class),
                $filters
            ];
        } else {
            $params = [
                $objectManager,
                $filterableAttributes,
                $filters
            ];
        }

        parent::__construct(...$params);
    }

    /**
     * @param \Magento\Catalog\Model\Layer $layer
     * @return array|\Magento\Catalog\Model\Layer\Filter\AbstractFilter[]
     */
    public function getFilters(\Magento\Catalog\Model\Layer $layer)
    {
        if (!$this->filtersLoaded) {
            $this->filters = $this->getAllFilters($layer);
            $this->filtersLoaded = true;
        } else {
            foreach ($this->filters as $key => $filter) {
                if ($filter->getRequestVar() === $this->data->getBrandAttributeCode() && $this->data->isBrandPage()) {
                    array_splice($this->filters, $key, 1);
                }
            }
        }
        return $this->filters;
    }

    /**
     * Get both top and left filters. And keep it in registry.
     *
     * @param \Magento\Catalog\Model\Layer $layer
     * @return \Magento\Catalog\Model\Layer\Filter\AbstractFilter[]
     */
    public function getAllFilters(\Magento\Catalog\Model\Layer $layer)
    {
        return parent::getFilters($layer);
    }
}
