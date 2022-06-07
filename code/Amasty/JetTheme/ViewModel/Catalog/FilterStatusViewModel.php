<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\ViewModel\Catalog;

use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Model\Layer\FilterList;
use Magento\Catalog\Model\Layer\AvailabilityFlagInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class FilterStatusViewModel implements ArgumentInterface
{
    /**
     * Catalog layer
     *
     * @var Layer
     */
    private $catalogLayer;

    /**
     * @var FilterList
     */
    private $filterList;

    /**
     * @var AvailabilityFlagInterface
     */
    private $visibilityFlag;

    public function __construct(
        Resolver $layerResolver,
        FilterList $filterList,
        AvailabilityFlagInterface $visibilityFlag
    ) {
        $this->catalogLayer = $layerResolver->get();
        $this->filterList = $filterList;
        $this->visibilityFlag = $visibilityFlag;
    }

    /**
     * Get all layer filters
     *
     * @return array
     */
    public function getFilters(): array
    {
        return $this->filterList->getFilters($this->catalogLayer);
    }

    /**
     * Get layer object
     *
     * @return Layer
     */
    public function getLayer(): Layer
    {
        return $this->catalogLayer;
    }

    /**
     * Check availability display layer block
     *
     * @return bool
     */
    public function canShowBlock(): bool
    {
        return $this->getLayer()->getCurrentCategory()->getDisplayMode() !== \Magento\Catalog\Model\Category::DM_PAGE
            && $this->visibilityFlag->isEnabled($this->getLayer(), $this->getFilters());
    }
}
