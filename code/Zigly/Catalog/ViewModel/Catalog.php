<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Catalog
 */
declare(strict_types=1);

namespace Zigly\Catalog\ViewModel;

use Magento\Customer\Model\SessionFactory as CustomerSession;


class Catalog implements \Magento\Framework\View\Element\Block\ArgumentInterface
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
     * @var CustomerSession
     */
    protected $customer;

    /**
     * @var $customer
     */
    protected $wishlist;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param CustomerSession $customer
     * @param \Magento\Wishlist\Model\Wishlist $wishlist
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Model\SessionFactory $sessionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        CustomerSession $customer,
        \Magento\Wishlist\Model\Wishlist $wishlist
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->sessionFactory = $sessionFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->customer = $customer;
        $this->wishlist = $wishlist;
    }

    public function getcustomerloggin(){
        return $this->sessionFactory->create()->isLoggedIn();
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
     * get Customer wishlist collection
     * @return []
     */
    public function getCustomerWishlist()
    {
        $customerId = $this->customer->create()->getCustomerId();
        $wishlist = $this->wishlist->loadByCustomerId($customerId)->getItemCollection();
        $customerWishlist = $wishlist->getData();
        $product_id = array();
        for ($i=0; $i < count($customerWishlist); $i++) {
            $product_id[$i] = $customerWishlist[$i]["product_id"];
        }
        return $product_id;
    }

}


