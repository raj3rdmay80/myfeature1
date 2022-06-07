<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomingService
 */
declare(strict_types=1);

namespace Zigly\GroomingService\Block\Center;

use Magento\Store\Model\StoreManager;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Zigly\Activities\Model\ActivitiesFactory;
use Magento\CatalogWidget\Block\Product\ProductsList;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Zigly\Plan\Model\ResourceModel\Plan\CollectionFactory;
use Magento\Review\Model\ResourceModel\Review\CollectionFactory as ReviewCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Mageplaza\Blog\Model\ResourceModel\Post\CollectionFactory as BlogPostCollection;

class Home extends \Magento\Framework\View\Element\Template
{

    /**
     * @var $productCollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var CollectionFactory
     */
    protected $plans;

    /**
     * @var ProductsList
     */
    private $productList;

    /**
     * @var ActivitiesFactory
     */
    protected $activities;

    /**
     * @var $scopeConfig
     */
    protected $scopeConfig;

    /**
     * @var $reviewCollection
     */
    protected $reviewCollection;

    /**
     * @var BlogPostCollection
     */
    protected $blogPostCollection;

    /**
     * @var Repository
     */
    protected $assetRepo;

    /**
     * @param Context $context
     * @param Repository $assetRepo
     * @param ScopeConfigInterface $scopeConfig
     * @param PriceCurrencyInterface $priceCurrency
     * @param CollectionFactory $planCollection
     * @param ProductsList $productList
     * @param StoreManager $storeManager
     * @param ActivitiesFactory $activitiesFactory
     * @param CollectionFactory $reviewCollection
     * @param ProductFactory $productCollectionFactory
     * @param BlogPostCollection $blogPostCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Repository $assetRepo,
        StoreManager $storeManager,
        ScopeConfigInterface $scopeConfig,
        PriceCurrencyInterface $priceCurrency,
        CollectionFactory $planCollection,
        ActivitiesFactory $activitiesFactory,
        ProductsList $productList,
        ReviewCollectionFactory $reviewCollection,
        BlogPostCollection $blogPostCollection,
        ProductCollectionFactory $productCollectionFactory
    )
    {
        $this->assetRepo = $assetRepo;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->priceCurrency =  $priceCurrency;
        $this->reviewCollection = $reviewCollection;
        $this->activities = $activitiesFactory;
        $this->productList = $productList;
        $this->plans = $planCollection;
        $this->blogPostCollection = $blogPostCollection;
        $this->productCollectionFactory = $productCollectionFactory;
        parent::__construct($context);
    }

    /**
     * Home product collection
     * @return ProductFactory
     */
    public function getHomeProductCollection()
    {
        $limit = '56';
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        /*$collection->addCategoriesFilter(['in' => [28]]);*/
        $collection->addAttributeToFilter('show_at_home',  array('eq' => '1'));
        $collection->addAttributeToSort('entity_id', 'DESC');
        $collection->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
        $collection->addAttributeToFilter('status',\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
        // ->orderRand()
        $collection->getSelect()->limit($limit);
        $collection->setFlag('has_stock_status_filter', true);
        $collection->joinField('stock_status', 'cataloginventory_stock_status', 'stock_status', 'product_id=entity_id', '{{table}}.stock_id=1', 'left'
            )->addFieldToFilter('stock_status', array('eq' => \Magento\CatalogInventory\Model\Stock\Status::STATUS_IN_STOCK));
        
        return $collection;
    }

    /**
     * Get Plan image
     */
    public function getBannerImage($image)
    {
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
        if (!empty($image)) {
            $imageurl = $mediaUrl."plan/feature/".$image;
        } else {
            $imageurl = $this->assetRepo->getUrl("Zigly_GroomingService::images/plans_banner.jpg");
        }
        return $imageurl;
    }

    public function getConfig($config_path)
    {
        return $this->storeManager->getStore()->getConfig($config_path);
    }

    /**
     * Get Plan collection based on petDetails
     * @return CollectionFactory
     */
    public function getPlans()
    {
        $planCollection = '';

        $planCollection = $this->plans->create()
            ->addFieldToFilter('species', '1')
            ->addFieldToFilter('plan_type', ['in' => [1,3]])
            ->addFieldToFilter('status', 1)
            ->setOrder('sort_order','ASC');
        return $planCollection;
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
     * Get Breed Type Name
     * @return string
     */
    public function getActivity($activityId)
    {
        $activity = "";
        $activity = $this->activities->create()->load($activityId);
        if ($activity->getIsActive()) {
            return $activity;
        }
        return $activity;
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

    /**
     * Get Blogs
     * @return string
     */
    public function getBlog()
    {
        $blogs = $this->blogPostCollection->create()->join(
            ['category' => 'mageplaza_blog_post_category'],
            'category.post_id = main_table.post_id and category.category_id = 5',
            'GROUP_CONCAT(category.category_id SEPARATOR \',\') as category'
        );
        $blogs->getSelect()->group('main_table.post_id')->limit(3);
        return $blogs;
    }

}