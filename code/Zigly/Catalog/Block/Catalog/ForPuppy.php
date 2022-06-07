<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Zigly\Catalog\Block\Catalog;

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
use Magento\Catalog\Model\CategoryFactory;

class ForPuppy extends AbstractProduct
{
    const FOR_DOG_CATEGORY_URL_KEY = 'for-dogs';

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

    /** @var CategoryFactory */
    private $categoryFactory;
    /**
     * Constructor
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param CollectionFactory $productCollectionFactory
     * @param CategoryFactory $categoryFactory
     * @param ProductsList $productList
     * @param BestSellersCollectionFactory $bestSellersCollectionFactory
     * @param MostViewedCollectionFactory $mostViewedCollectionFactory
     */
    public function __construct(
        CategoryCollectionFactory $categoryCollectionFactory,
        CollectionFactory $productCollectionFactory,
        ProductsList $productList,
        CategoryFactory $categoryFactory,
        BestSellersCollectionFactory $bestSellersCollectionFactory,
        MostViewedCollectionFactory $mostViewedCollectionFactory,
        Context $context,
        array $data = []
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productList = $productList;
        $this->_categoryFactory = $categoryFactory;
        $this->bestSellersCollectionFactory = $bestSellersCollectionFactory;
        $this->mostViewedCollection = $mostViewedCollectionFactory;
        parent::__construct($context, $data);
    }

   /**
     * get current category
     * @return void
     */
    public function getForDogCategory()
    {
        $category = $this->_categoryFactory->create()->loadByAttribute('url_key', self::FOR_DOG_CATEGORY_URL_KEY);
        return $category;
    }

    /**
     * get catalog child collection to display products
     * @return void
     */
    public function getChildCollectionToDisplayProducts()
    {
        $level= (int)$this->getForDogCategory()->getLevel() + 1;
        $categories = $this->categoryCollectionFactory->create();
        $categories->addAttributeToSelect('*');
        $categories->addAttributeToFilter('level' , $level);
        $categories->addAttributeToFilter('parent_id' , $this->getForDogCategory()->getId());
        $categories->addAttributeToFilter('is_active' , 1);
        $categories->addAttributeToFilter('include_in_menu' , 1);
        return $categories;
    }

    /**
     * get collection of best-seller products
     * @return mixed
     */
    public function getChildProductCollection($categoryId)
    {
        $lifeStage = $this->getLifeStage();
        $collection = $this->productCollectionFactory->create();
        $collection->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addAttributeToSelect('*')
            ->addCategoriesFilter(array('in' => $this->getChildCatgoryId($categoryId)));
        if ($lifeStage) {
            $collection->addAttributeToFilter(self::FOR_DOG_CATEGORY_LIFE_STAGE_ATTRIBUTE, $lifeStage);
        }
        $collection->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
            ->getSelect()->orderRand()->limit('8');
        return $collection;
    }

    /**
     * get catalog child collection of a category
     * @return void
     */
    public function getChildCatgoryId($categoryId)
    {
        $categoryIds = [];
        $level= (int)$this->getForDogCategory()->getLevel() + 2;
        $categories = $this->categoryCollectionFactory->create();
        $categories->addAttributeToSelect('entity_id');
        $categories->addAttributeToFilter('level' , $level);
        $categories->addAttributeToFilter('parent_id' , $categoryId);
        $categories->addAttributeToFilter('is_active' , 1);
        $categories->addAttributeToFilter('include_in_menu' , 1);
        foreach ($categories as $category) {
            $categoryIds[] = $category->getEntityId();
        }
        return $categoryIds;
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
        $lifeStage = $this->getLifeStage();
        if($lifeStage) {
            $glue = '?';
            if (strpos($url, $glue) !== false) {
                $glue = '&';
            }
            $url .= $glue.self::FOR_DOG_CATEGORY_LIFE_STAGE_ATTRIBUTE.'='.urlencode($lifeStage);
        }
        return $url;
    }

}
