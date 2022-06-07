<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_ChangeLabel
 */
declare(strict_types=1);

namespace Zigly\ChangeLabel\Block\Homepage;

use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Amasty\ShopbyBase\Model\ResourceModel\OptionSetting\CollectionFactory as BrandCollection;

class BrandListing extends \Magento\Framework\View\Element\Template
{
    /**
     * @var BrandCollection
     */
    protected $brandCollection;

    /**
     * @var StoreManagerInterface
    */
    protected $storeManager;

    /**
     * @param Context $context
     * @param BrandCollection $brandCollection
     * @param StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        Context $context,
        BrandCollection $brandCollection,
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
        $this->brandCollection = $brandCollection;
        parent::__construct($context);
    }

    public function getBrand()
    {
        try{
            $brandLimit = $this->getConfig('brand/limit/brand_listing_limit');
            $brandListing = $this->brandCollection->create()->join(['option' => 'eav_attribute_option_value'], 'option.option_id = main_table.value', 'IF(main_table.title != \'\', main_table.title, option.value) as title');
            $brandListing->addFieldToSelect('*')
                 ->addFieldToFilter('home_featured', ['eq' => 1])
                 ->setOrder('home_sort_order','ASC');
            if($brandLimit != 0) {
                $brandListing->setPageSize($brandLimit);
            }else{
                $brandListing->setPageSize(24);
            }
            return $brandListing;
        } catch (\Exception $e) {
            throw new NoSuchEntityException(__($e->getMessage()), $e);
        }
    }

    /**
     * Get Brand Image
     */
    public function getBrandImage($image)
    {
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        if (!empty($image)) {
            $imageurl = $mediaUrl."amasty/shopby/option_images/".$image;
        } else {
            $imageurl = $mediaUrl.'catalog/product/placeholder/'.$this->getConfig('catalog/placeholder/thumbnail_placeholder');
        }
        return $imageurl;
    }

    public function getConfig($config_path)
    {
        return $this->storeManager->getStore()->getConfig($config_path);
    }

    public function getViewUrl()
    {
        return $this->getConfig('brand/limit/brand_view_url');
    }


    public function getEnableViewMore()
    {
        return $this->getConfig('brand/limit/enable_view_more_link');
    }
}
