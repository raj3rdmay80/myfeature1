<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Managepets
 */
namespace Zigly\Managepets\Block;

use Zigly\Managepets\Helper\Data;
use Magento\Store\Model\StoreManager;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\View\Asset\Repository;
use Zigly\Managepets\Model\ManagepetsFactory;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class Managepets extends \Magento\Framework\View\Element\Template
{
    /**
     * @var $managepetsFactory
     */
    protected $managepetsFactory;

    /**
     * @var $assetRepository
     */
    protected $assetRepository;

    /**
     * @param Context $context
     * @param Data $helperdata
     * @param JsonHelper $jsonHelper
     * @param SessionFactory $customer
     * @param StoreManager $storeManager
     * @param Repository $assetRepository
     * @param TimezoneInterface $timezone
     * @param ManagepetsFactory $managepetsFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $helperdata,
        JsonHelper $jsonHelper,
        SessionFactory $customer,
        StoreManager $storeManager,
        Repository $assetRepository,
        TimezoneInterface $timezone,
        ManagepetsFactory $managepetsFactory,
        array $data = []
    ) {
        $this->managepetsFactory = $managepetsFactory;
        $this->assetRepository = $assetRepository;
        $this->customer = $customer;
        $this->storeManager = $storeManager;
        $this->helperdata = $helperdata;
        $this->timezone = $timezone;
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->pageConfig->getTitle()->set(__('Manage Pets'));
        if ($this->getManagepetsCollection()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'custom.history.pager'
            )->setAvailableLimit([5 => 5, 10 => 10, 15 => 15, 20 => 20])
            ->setShowPerPage(true)->setCollection(
                $this->getManagepetsCollection()
            );
            $this->setChild('pager', $pager);
            $this->getManagepetsCollection()->load();
        }
        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getPetAssetImage($path)
    {
        $asset = $this->assetRepository->createAsset('Zigly_Managepets::'.$path.'');
        return $asset->getUrl();
    }

    public function getPetimage($imageurls){
        $images = explode(",",$imageurls);
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
        if(!empty($images)){
            foreach($images as $ikey => $image){
                if($image){
                    $imageurl = $mediaUrl."zigly/".$image;
                    break;
                }
            }

        }else{
            $imageurl = $mediaUrl.'catalog/product/placeholder/'.$this->getConfig('catalog/placeholder/thumbnail_placeholder');
        }
        return $imageurl;

    }
    public function getConfig($config_path)
    {
            return $this->storeManager->getStore()->getConfig($config_path);
    }

    public function getplaceholderimage(){
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
        return $mediaUrl.'catalog/product/placeholder/'.$this->getConfig('catalog/placeholder/thumbnail_placeholder');
    }
    public function getManagepetsCollection()
    {
        $page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
        $pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 5;
        $customerid = $this->customer->create()->getCustomer()->getId();
        $collection = $this->managepetsFactory->create()->getCollection()->addFieldtoFilter('main_table.customer_id',$customerid);
        $collection->getSelect()
        ->joinLeft(
            ['species'=>'zigly_species_species'],
            "main_table.type = species.species_id",
            [
                'speciesname' => 'species.name'
            ]
        );
        $collection->getSelect()
        ->joinLeft(
            ['breed'=>'zigly_species_breed'],
            "main_table.breed = breed.breed_id",
            [
                'breedname' => 'breed.name'
            ]
        );
        $collection->setPageSize($pageSize);
        $collection->setCurPage($page);
        return $collection;
    }

    public function getaccountManagepetsCollection()
    {
        $customerid = $this->customer->create()->getCustomer()->getId();
        $collection = $this->managepetsFactory->create()->getCollection()->addFieldtoFilter('main_table.customer_id',$customerid);
        $collection->getSelect()
        ->joinLeft(
            ['species'=>'zigly_species_species'],
            "main_table.type = species.species_id",
            [
                'speciesname' => 'species.name'
            ]
        );
        $collection->getSelect()
        ->joinLeft(
            ['breed'=>'zigly_species_breed'],
            "main_table.breed = breed.breed_id",
            [
                'breedname' => 'breed.name'
            ]
        );
        // $collection->setPageSize(3);
        // $collection->setCurPage(1);
        return $collection;
    }

    public function getGenders(){
        return $this->helperdata->getGenders();
    }
    public function getDobvalue($datevalue = null){
        $dobvalue = '';
        if($datevalue){
            $dobvalue = str_replace(" 00:00:00", "", $datevalue);
        }
        return $dobvalue;
    }

    public function getDob($date)
    {
        return $this->timezone->date(new \DateTime($date))->format('Y-m-d');
    }

}
