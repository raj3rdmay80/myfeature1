<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Catalog
 */
declare(strict_types=1);

namespace Zigly\Catalog\Block;

class ProductList extends \Magento\Framework\View\Element\Template
{

    /**
     * @var $productCollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var $scopeConfig
     */
    protected $scopeConfig;

    /**
     * @var $reviewCollection
     */
    protected $reviewCollection; 

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Review\Model\ResourceModel\Review\CollectionFactory $reviewCollection
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Review\Model\ResourceModel\Review\CollectionFactory $reviewCollection,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->priceCurrency =  $priceCurrency;
        $this->reviewCollection = $reviewCollection;
        $this->productCollectionFactory = $productCollectionFactory;
        parent::__construct($context);
    }

    /**
     * get favorite product collection
     * @return void
     */
    public function getFavoriteProductCollection()
    {
        $limit = $this->scopeConfig->getValue('listingpage/favorite/customer_favorite',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addAttributeToFilter('customer_favorites',  array('eq' => '1'));
        $collection->addAttributeToSort('entity_id', 'DESC');
        $collection->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
        $collection->addAttributeToFilter('status',\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
        $collection->getSelect()->orderRand()->limit($limit);
        $collection->setFlag('has_stock_status_filter', true);
        $collection->joinField('stock_status', 'cataloginventory_stock_status', 'stock_status', 'product_id=entity_id', '{{table}}.stock_id=1', 'left'
            )->addFieldToFilter('stock_status', array('eq' => \Magento\CatalogInventory\Model\Stock\Status::STATUS_IN_STOCK));
        return $collection;
    }

    /**
     * get exclusive product collection
     * @return void
     */
    public function getExclusiveProductCollection()
    {
        $limit = $this->scopeConfig->getValue('listingpage/exclusive/exclusive_at_zigly',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addAttributeToFilter('exclusively_at_zigly',  array('eq' => '1'));
        $collection->addAttributeToSort('entity_id', 'DESC');
        $collection->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
        $collection->addAttributeToFilter('status',\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
        $collection->getSelect()->orderRand()->limit($limit);
        $collection->joinField('stock_status', 'cataloginventory_stock_status', 'stock_status', 'product_id=entity_id', '{{table}}.stock_id=1', 'left'
            )->addFieldToFilter('stock_status', array('eq' => \Magento\CatalogInventory\Model\Stock\Status::STATUS_IN_STOCK));
        return $collection;
    }

    /**
     * get product review
     * @return void
     */
    public function getProductReviewCount($productId)
    {
        $collection = $this->reviewCollection->create()->addStatusFilter(\Magento\Review\Model\Review::STATUS_APPROVED)->addEntityFilter('product',$productId);
        return $collection->getSize();
    }

    /**
     * Convert and format price value for current application store
     *
     * @param   float $value
     * @param   bool $format
     * @param   bool $includeContainer
     * @return  float|string
     */
    public function currency($value, $format = true, $includeContainer = true)
    {
        return $format
            ? $this->priceCurrency->convertAndFormat($value, $includeContainer)
            : $this->priceCurrency->convert($value);
    }

}