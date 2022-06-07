<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Catalog
 */

namespace Zigly\Catalog\Plugin\Model;

use Magento\Store\Model\StoreManagerInterface;

class Config
{
    /**
     * @var StoreManagerInterface $storeManager
     */
    protected $storeManager;

    /**
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }
    public function afterGetAttributeUsedForSortByArray(\Magento\Catalog\Model\Config $catalogConfig, $options)
    {
        $options['position'] = 'Relevance';
        return $options;
    }
}