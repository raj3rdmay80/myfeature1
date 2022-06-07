<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Catalog
 */
declare(strict_types=1);

namespace Zigly\Catalog\Block\Homepage;

// use Magento\Framework\View\Element\Template\Context;
use Magento\Catalog\Block\Product\Context;
use Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory as BestSellersCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\CatalogWidget\Block\Product\ProductsList;
use Magento\Catalog\Model\Product;
use Magento\Framework\Pricing\Render;
use Magento\Reports\Model\ResourceModel\Product\CollectionFactory as MostViewedCollectionFactory;


/**
 * Our Product Tabs
 */
class OurProductTabs extends AbstractProduct
{

    /**
     * @var CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var BestSellersCollectionFactory
     */
    protected $bestSellersCollectionFactory;

    /**
     * @var ProductsList
     */
    private $productList;

    /**
     * @var MostViewedCollectionFactory
     */
    protected $mostViewedCollectionFactory;

    /**
     * Constructor
     * @param CollectionFactory $productCollectionFactory
     * @param ProductsList $productList
     * @param BestSellersCollectionFactory $bestSellersCollectionFactory
     * @param MostViewedCollectionFactory $mostViewedCollectionFactory
     */
    public function __construct(
        CollectionFactory $productCollectionFactory,
        ProductsList $productList,
        BestSellersCollectionFactory $bestSellersCollectionFactory,
        MostViewedCollectionFactory $mostViewedCollectionFactory,
        Context $context,
        array $data = []
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productList = $productList;
        $this->bestSellersCollectionFactory = $bestSellersCollectionFactory;
        $this->mostViewedCollection = $mostViewedCollectionFactory;
        parent::__construct($context, $data);
    }


    /**
     * get collection of best-seller products
     * @return mixed
     */
    public function getBestProductCollection()
    {
        $productIds = [];
        $collection = $this->productCollectionFactory->create();
        $collection->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addAttributeToSelect('*');
        $collection->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
            ->addAttributeToFilter('best_selling', 1)->setPageSize('8');
        return $collection;
    }

    /**
     * get collection of Popular products
     * @return mixed
     */
    public function getMostViewedProductCollection()
    {
        $productIds = [];
        $collection = $this->productCollectionFactory->create();
        $collection->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addAttributeToSelect('*');
        $collection->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
            ->addAttributeToFilter('popular', 1)->setPageSize('8');
        return $collection;
    }

    /**
     * get collection of New arrival products
     * @return mixed
     */
    public function getNewProductCollection()
    {
        $productIds = [];
        $collection = $this->productCollectionFactory->create();
        $collection->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addAttributeToSelect('*');
        $collection->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
            ->addAttributeToFilter('new_arrival', 1)->setPageSize('8');
        return $collection;
    }

    /**
     * @param $product
     * @return array
     */
    public function getAddToCartPostParams($product): array
    {
        return $this->productList->getAddToCartPostParams($product);
    }

    /**
     * @param Product $product
     * @param null $priceType
     * @param string $renderZone
     * @param array $arguments
     * @return string
     */
    public function getProductPriceHtml(
        Product $product,
        $priceType = null,
        $renderZone = Render::ZONE_ITEM_LIST,
        array $arguments = []
    ): string {
        return $this->productList->getProductPriceHtml($product, $priceType, $renderZone, $arguments);
    }

}