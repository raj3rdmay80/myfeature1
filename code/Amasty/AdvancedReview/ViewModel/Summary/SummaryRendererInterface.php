<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


declare(strict_types=1);

namespace Amasty\AdvancedReview\ViewModel\Summary;

use Magento\Catalog\Model\Product;
use Magento\Review\Model\ResourceModel\Review\Collection as ReviewCollection;

interface SummaryRendererInterface
{
    public function render(ReviewCollection $collection, Product $product): string;
}
