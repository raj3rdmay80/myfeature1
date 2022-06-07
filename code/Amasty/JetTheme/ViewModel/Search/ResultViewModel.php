<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\ViewModel\Search;

use Magento\CatalogSearch\Helper\Data;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class ResultViewModel implements ArgumentInterface
{
    /**
     * Catalog search data
     *
     * @var Data
     */
    protected $catalogSearchData;

    public function __construct(
        Data $catalogSearchData
    ) {
        $this->catalogSearchData = $catalogSearchData;
    }

    /**
     * Get search query text
     *
     * @return string
     */
    public function getQueryText(): string
    {
        return $this->catalogSearchData->getEscapedQueryText();
    }
}
