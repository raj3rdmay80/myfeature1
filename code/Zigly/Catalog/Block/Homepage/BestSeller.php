<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Zigly\Catalog\Block\Homepage;

use Magento\Catalog\Block\Product\Context;
use Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory as BestSellersCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\CatalogWidget\Block\Product\ProductsList;
use Magento\Catalog\Model\Product;
use Magento\Framework\Pricing\Render;
use Magento\Reports\Model\ResourceModel\Product\CollectionFactory as MostViewedCollectionFactory;


/**
 * Best Seller Tabs
 */
class BestSeller extends AbstractProduct
{
    const FILTER_MIN_PRICE = 1;
    const FILTER_UNDER_PRICE_199 = 199;
    const FILTER_UNDER_PRICE_499 = 499;
    const FILTER_UNDER_PRICE_999 = 999;
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
     * Get product collection under 199
     * @return mixed
     */
    public function getProductCollectionUnder199()
    {
        $productIds = [];
        $collection = $this->productCollectionFactory->create();
        $collection->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addAttributeToSelect('*');
        $collection->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
            ->addAttributeToFilter('best_seller_home', 1)
            ->setPageSize('8')
            ->getSelect()
            ->where("price_index.final_price >= " . self::FILTER_MIN_PRICE)
            ->where("price_index.final_price < " . self::FILTER_UNDER_PRICE_199)
            ->orderRand();
        return $collection;
    }


    /**
     * Get product collection under 499
     * @return mixed
     */
    public function getProductCollectionUnder499()
    {
        $productIds = [];
        $collection = $this->productCollectionFactory->create();
        $collection->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addAttributeToSelect('*');
        $collection->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
            ->addAttributeToFilter('best_seller_home', 1)
            ->setPageSize('8')
            ->getSelect()
            ->where("price_index.final_price >= " . self::FILTER_UNDER_PRICE_199)
            ->where("price_index.final_price < " . self::FILTER_UNDER_PRICE_499)
            ->orderRand();
        return $collection;
    }

    /**
     * Get product collection under 999
     * @return mixed
     */
    public function getProductCollectionUnder999()
    {
        $productIds = [];
        $collection = $this->productCollectionFactory->create();
        $collection->addFinalPrice()
            ->addAttributeToSelect('*');
        $collection->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
            ->addAttributeToFilter('best_seller_home', 1)
            ->setPageSize('8')
            ->getSelect()
            ->where("price_index.final_price >= ".self::FILTER_UNDER_PRICE_499)
            ->where("price_index.final_price < ".self::FILTER_UNDER_PRICE_999)
            ->orderRand();
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
