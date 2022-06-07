<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_VetConsulting
 */
declare(strict_types=1);

namespace Zigly\VetConsulting\Block\Vet;

use Zigly\Managepets\Helper\Data;
use Magento\Customer\Model\SessionFactory;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\View\Asset\Repository;
use Magento\Directory\Model\CurrencyFactory;
use Zigly\Managepets\Model\ManagepetsFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Zigly\VetConsulting\Model\SessionFactory as VetSession;

class Consulting extends \Magento\Framework\View\Element\Template
{

    /**
     * @var $managepetsFactory
     */
    protected $managepetsFactory;

    /**
     * @var CurrencyFactory
     */
    protected $currencyFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Constructor
     * @param Data $helperdata
     * @param Context $context
     * @param SessionFactory $customer
     * @param Repository $assetRepository
     * @param TimezoneInterface $timezone
     * @param CustomerFactory $customerFactory
     * @param CurrencyFactory $currencyFactory
     * @param StoreManagerInterface $storeManager
     * @param ManagepetsFactory $managepetsFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $helperdata,
        SessionFactory $customer,
        Repository $assetRepository,
        TimezoneInterface $timezone,
        CustomerFactory $customerFactory,
        CurrencyFactory $currencyFactory,
        StoreManagerInterface $storeManager,
        VetSession $vetSession,
        ManagepetsFactory $managepetsFactory,
        array $data = []
    ) {
        $this->customer = $customer;
        $this->timezone = $timezone;
        $this->helperdata = $helperdata;
        $this->storeManager = $storeManager;
        $this->assetRepository = $assetRepository;
        $this->customerFactory = $customerFactory;
        $this->currencyFactory = $currencyFactory;
        $this->managepetsFactory = $managepetsFactory;
        $this->vetSession = $vetSession;
        parent::__construct($context, $data);
    }

    /*
    * Get currency symbol
    */
    public function getLocation()
    {
        $consultSession = $this->vetSession->create()->getVet();
        if (!empty($consultSession['detected_place'])) {
            return $consultSession['detected_place'];
        }
        return false;
    }

    /*
    * Get Existing address
    */
    public function currencySymbol()
    {
        $currencyCode = $this->storeManager->getStore()->getCurrentCurrencyCode();
        $currency = $this->currencyFactory->create()->load($currencyCode);
        return $currency->getCurrencySymbol();
    }

    /*
    * Get pet dob
    */
    public function getDob($date)
    {
        return $this->timezone->date(new \DateTime($date))->format('Y-m-d');
    }

    /*
    * Get pets
    */
    public function getaccountManagepetsCollection()
    {
        $customerid = $this->customer->create()->getCustomer()->getId();
        $collection = $this->managepetsFactory->create()->getCollection()->addFieldtoFilter('customer_id',$customerid);
        $collection->getSelect()
        ->joinLeft(
            ['species'=>'zigly_species_species'],
            "type = species.species_id",
            [
                'speciesname' => 'species.name'
            ]
        );
        $collection->getSelect()
        ->join(
            ['breed'=>'zigly_species_breed'],
            "breed = breed.breed_id",
            [
                'breedname' => 'breed.name'
            ]
        );
        return $collection;
    }

    /*
    * Get gender
    */
    public function getGenders()
    {
        return $this->helperdata->getGenders();
    }

    /*
    * Get place holder image
    */
    public function getplaceholderimage()
    {
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
        return $mediaUrl.'catalog/product/placeholder/'.$this->getConfig('catalog/placeholder/thumbnail_placeholder');
    }

    /*
    * Get pet asset image
    */
    public function getPetAssetImage($path)
    {
        $asset = $this->assetRepository->createAsset('Zigly_Managepets::'.$path.'');
        return $asset->getUrl();
    }

    /*
    * Get pet image
    */
    public function getPetimage($imageurls)
    {
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

    /*
    * Get config
    */
    public function getConfig($config_path)
    {
        return $this->storeManager->getStore()->getConfig($config_path);
    }

    /*
    * Get Start Hours
    */
    public function getStartHr()
    {
        return $this->getConfig('zigly_timeslot_vetconsulting/start/hours');
    }

    /*
    * Get Start Minutes
    */
    public function getStartMin()
    {
        return $this->getConfig('zigly_timeslot_vetconsulting/start/minutes');
    }

    /*
    * Get End Hrs
    */
    public function getEndHr()
    {
        return $this->getConfig('zigly_timeslot_vetconsulting/end/hours');
    }

    /*
    * Get End Minutes
    */
    public function getEndMin()
    {
        return $this->getConfig('zigly_timeslot_vetconsulting/end/minutes');
    }
}

