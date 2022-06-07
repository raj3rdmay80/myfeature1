<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Catalog
 */
declare(strict_types=1);


namespace Zigly\Catalog\Block\Catalog\ForthLevel;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Framework\Registry;
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
class ProductTabs extends AbstractProduct
{
    const FOR_DOG_CATEGORY_BREED_SIZE_ATTRIBUTE = 'breedsize';

    const FOR_DOG_CATEGORY_LIFE_STAGE_ATTRIBUTE = 'lifestage';

    /** @var CollectionFactory */
    protected $productCollectionFactory;

    /** @var CategoryCollectionFactory */
    protected $categoryCollectionFactory;

    /** @var BestSellersCollectionFactory */
    protected $bestSellersCollectionFactory;

    /** @var ProductsList */
    private $productList;

    /** @var MostViewedCollectionFactory */
    protected $mostViewedCollectionFactory;

    /** @var Registry */
    protected $registry;
    /**
     * Constructor
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param CollectionFactory $productCollectionFactory
     * @param Registry $registry
     * @param ProductsList $productList
     * @param BestSellersCollectionFactory $bestSellersCollectionFactory
     * @param MostViewedCollectionFactory $mostViewedCollectionFactory
     */
    public function __construct(
        CategoryCollectionFactory $categoryCollectionFactory,
        CollectionFactory $productCollectionFactory,
        ProductsList $productList,
        Registry $registry,
        BestSellersCollectionFactory $bestSellersCollectionFactory,
        MostViewedCollectionFactory $mostViewedCollectionFactory,
        Context $context,
        array $data = []
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productList = $productList;
        $this->coreRegistry = $registry;
        $this->bestSellersCollectionFactory = $bestSellersCollectionFactory;
        $this->mostViewedCollection = $mostViewedCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * get current category
     * @return void
     */
    public function getCurrentCategory()
    {
        return $this->coreRegistry->registry('current_category');
    }

    /**
     * get catalog child collection to display products
     * @return void
     */
    public function getChildCollectionToDisplayProducts()
    {
        $level= (int)$this->getCurrentCategory()->getLevel() + 1;
        $categories = $this->categoryCollectionFactory->create();
        $categories->addAttributeToSelect('*');
        $categories->addAttributeToFilter('level' , $level);
        $categories->addAttributeToFilter('parent_id' , $this->getCurrentCategory()->getId());
        $categories->addAttributeToFilter('is_active' , 1);
        $categories->addAttributeToFilter('include_in_menu' , 1);
        $categories->addAttributeToFilter('display_on_landing_page' , 1);
        return $categories;
    }

    /**
     * get collection of best-seller products
     * @return mixed
     */
    public function getBestProductCollection($categoryId)
    {
        $breedSize = $this->getBreedSize();
        $lifeStage = $this->getLifeStage();
        $productIds = [];
        $collection = $this->productCollectionFactory->create();
        $collection->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addAttributeToSelect('*')
            ->addCategoriesFilter(array('in' => [$categoryId]));
        if ($breedSize) {
            $collection->addAttributeToFilter(self::FOR_DOG_CATEGORY_BREED_SIZE_ATTRIBUTE, $breedSize);
        } elseif ($lifeStage) {
            $collection->addAttributeToFilter(self::FOR_DOG_CATEGORY_LIFE_STAGE_ATTRIBUTE, $lifeStage);
        }
        $collection->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
            ->addAttributeToFilter('catalog_best_selling', 1)->getSelect()->limit('8');
        return $collection;
    }

    /**
     * get collection of Popular products
     * @return mixed
     */
    public function getPopuluarProductCollection($categoryId)
    {
        $breedSize = $this->getBreedSize();
        $lifeStage = $this->getLifeStage();
        $productIds = [];
        $collection = $this->productCollectionFactory->create();
        $collection->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addAttributeToSelect('*')
            ->addCategoriesFilter(array('in' => [$categoryId]));
        if ($breedSize) {
            $collection->addAttributeToFilter(self::FOR_DOG_CATEGORY_BREED_SIZE_ATTRIBUTE, $breedSize);
        } elseif ($lifeStage) {
            $collection->addAttributeToFilter(self::FOR_DOG_CATEGORY_LIFE_STAGE_ATTRIBUTE, $lifeStage);
        }
        $collection->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
            ->addAttributeToFilter('catalog_popular', 1)->getSelect()->limit('8');
        return $collection;
    }

    /**
     * get collection of New arrival products
     * @return mixed
     */
    public function getNewProductCollection($categoryId)
    {
        $breedSize = $this->getBreedSize();
        $lifeStage = $this->getLifeStage();
        $productIds = [];
        $collection = $this->productCollectionFactory->create();
        $collection->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addAttributeToSelect('*')
            ->addCategoriesFilter(array('in' => [$categoryId]));
        if ($breedSize) {
            $collection->addAttributeToFilter(self::FOR_DOG_CATEGORY_BREED_SIZE_ATTRIBUTE, $breedSize);
        } elseif ($lifeStage) {
            $collection->addAttributeToFilter(self::FOR_DOG_CATEGORY_LIFE_STAGE_ATTRIBUTE, $lifeStage);
        }
        $collection->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
            ->addAttributeToFilter('catalog_new_arrival', 1)->getSelect()->limit('8');
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

    /**
     * Get breed size param
     * 
     * @return string
     */
    public function getBreedSize() {
        return $this->getRequest()->getParam(self::FOR_DOG_CATEGORY_BREED_SIZE_ATTRIBUTE);
    }

    /**
     * Get life stage param
     * 
     * @return string
     */
    public function getLifeStage() {
        return $this->getRequest()->getParam(self::FOR_DOG_CATEGORY_LIFE_STAGE_ATTRIBUTE);
    }

    /**
     * Get category url
     * 
     * @return string
     */
    public function getCategoryUrl($url) {
        $breedSize = $this->getBreedSize();
        $lifeStage = $this->getLifeStage();
        if($breedSize) {
            $glue = '?';
            if (strpos($url, $glue) !== false) {
                $glue = '&';
            }
            $url .= $glue.self::FOR_DOG_CATEGORY_BREED_SIZE_ATTRIBUTE.'='.urlencode($breedSize);
        } elseif($lifeStage) {
            $glue = '?';
            if (strpos($url, $glue) !== false) {
                $glue = '&';
            }
            $url .= $glue.self::FOR_DOG_CATEGORY_LIFE_STAGE_ATTRIBUTE.'='.urlencode($lifeStage);
        }
        return $url;
    }

}