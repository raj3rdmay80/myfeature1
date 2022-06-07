<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomingService
 */
declare(strict_types=1);

namespace Zigly\GroomingService\Block\Grooming;

use Zigly\Managepets\Helper\Data;
use Magento\Customer\Model\SessionFactory;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\View\Asset\Repository;
use Magento\Directory\Model\CurrencyFactory;
use Zigly\Managepets\Model\ManagepetsFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\AddressFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Zigly\Cityscreen\Model\ResourceModel\Cityscreen\CollectionFactory as CityCollection;
use Zigly\Plan\Model\ResourceModel\Plan\CollectionFactory;
// use Magento\Framework\App\Config\ScopeConfigInterface;

class Index extends \Magento\Framework\View\Element\Template
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
     * @var CookieManagerInterface
     */
    private $cookieManager;

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
     * @param AddressFactory $address
     * @param CookieManagerInterface $cookieManager
     * @param ManagepetsFactory $managepetsFactory
     * @param ScopeConfigInterface $scopeConfig
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
        CookieManagerInterface $cookieManager,
        CollectionFactory $planCollection,
        CityCollection $cities,
        AddressFactory $address,
        ManagepetsFactory $managepetsFactory,
        array $data = []
    ) {
        $this->customer = $customer;
        $this->managepetsFactory = $managepetsFactory;
        $this->timezone = $timezone;
        $this->helperdata = $helperdata;
        $this->storeManager = $storeManager;
        $this->assetRepository = $assetRepository;
        $this->customerFactory = $customerFactory;
        $this->currencyFactory = $currencyFactory;
        $this->cookieManager = $cookieManager;
        $this->cities = $cities;
        $this->plans = $planCollection;
        $this->address = $address;
        parent::__construct($context, $data);
    }

    public function getAddressCollection()
    {
        $city = $this->cookieManager->getCookie('city_screen');
        $customer = $this->customer->create();
        $customerId = $customer->getId();
        $address =  $this->address->create()->getCollection()->addFieldToFilter('parent_id',$customerId);
        if (!empty($city) && (strcasecmp($city, 'New delhi') == 0)) {
            $address->addFieldToFilter('city', ['in' => ['New Delhi', 'Delhi']]);
        } else {
            $address->addFieldToFilter('city', ['eq' => $city]);
        }
        $address->getData();
        return $address;
    }

    public function currencySymbol()
    {
        $currencyCode = $this->storeManager->getStore()->getCurrentCurrencyCode();
        $currency = $this->currencyFactory->create()->load($currencyCode);
        return $currency->getCurrencySymbol();
    }

    public function getDob($date)
    {
        return $this->timezone->date(new \DateTime($date))->format('Y-m-d');
    }

    /**
     * Get Plan collection based on petDetails
     * @return CollectionFactory
     */
    public function getPlans()
    {
        $planCollection = [];
        $city = $this->cookieManager->getCookie('city_screen');
        if (!empty($city) && (strcasecmp($city, 'New delhi') == 0)) {
            $city = "Delhi";
        }
        $cities = $this->cities->create()->addFieldToSelect('cityscreen_id')->addFieldToFilter('city', ['eq' => $city]);
        $citiesArray = [];
        foreach ($cities as $city) {
            $citiesArray[] = ['finset' => $city->getCityscreenId()];
        }

        $planCollection = $this->plans->create()
            ->addFieldToFilter('plan_type', ['in' => [1,3]]);
        if (count($citiesArray)) {
            $planCollection->addFieldToFilter(
                    ['applicable_cities'],
                    [$citiesArray]
                );
        }
        $planCollection->addFieldToFilter('status', 1)
            ->setOrder('sort_order','ASC');
        return $planCollection;
    }

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

    public function getGenders()
    {
        return $this->helperdata->getGenders();
    }

    public function getplaceholderimage()
    {
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
        return $mediaUrl.'catalog/product/placeholder/'.$this->getConfig('catalog/placeholder/thumbnail_placeholder');
    }

    public function getPetAssetImage($path)
    {
        $asset = $this->assetRepository->createAsset('Zigly_Managepets::'.$path.'');
        return $asset->getUrl();
    }

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

    public function getConfig($config_path)
    {
        return $this->storeManager->getStore()->getConfig($config_path);
    }

    /*
    * Get Start Hours
    */
    public function getStartHr()
    {
        return $this->getConfig('zigly_timeslot/start/hours');
    }

    /*
    * Get Start Minutes
    */
    public function getStartMin()
    {
        return $this->getConfig('zigly_timeslot/start/minutes');
    }

    /*
    * Get End Hrs
    */
    public function getEndHr()
    {
        return $this->getConfig('zigly_timeslot/end/hours');
    }

    /*
    * Get End Minutes
    */
    public function getEndMin()
    {
        return $this->getConfig('zigly_timeslot/end/minutes');
    }
}