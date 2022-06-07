<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_ProductApi
 */
declare(strict_types=1);

namespace Zigly\ProductApi\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface ProductRepositoryInterface
{
    /**
     * @api
     * @return \Magento\Catalog\Api\Data\ProductSearchResultsInterface
     */
    public function ProductCollectionApi();
}