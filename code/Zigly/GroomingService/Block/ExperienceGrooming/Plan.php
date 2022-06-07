<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomingService
 */
declare(strict_types=1);

namespace Zigly\GroomingService\Block\ExperienceGrooming;

use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\Session;
use Magento\Store\Model\StoreManager;
use Zigly\Managepets\Model\ManagepetsFactory;
use Zigly\Species\Model\BreedFactory;
use Zigly\Activities\Model\ActivitiesFactory;
use Zigly\Plan\Model\ResourceModel\Plan\CollectionFactory;
use Zigly\GroomingService\Model\Session as GroomSession;
use Zigly\Plan\Model\PlanFactory;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Zigly\Cityscreen\Model\ResourceModel\Cityscreen\CollectionFactory as CityCollection;

/**
 * Show plans for selection
 */
class Plan extends \Magento\Framework\View\Element\Template
{

    /**
     * @var CollectionFactory
     */
    protected $plans;

    /**
     * @var Session
     */
    protected $customer;

    /**
     * @var ManagepetsFactory
     */
    protected $petdetails;

    /**
     * @var ManagepetsFactory
     */
    protected $pets;

    /**
     * @var BreedFactory
     */
    protected $breed;

    /**
     * @var ActivitiesFactory
     */
    protected $activities;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var GroomSession
     */
    protected $groomingSession;

    /**
     * @var groom session's value
     */
    protected $groomSession;

    /**
     * @var CookieManagerInterface
     */
    private $cookieManager;

    /**
     * @var CityCollection
     */
    protected $cities;

    /**
     * @var PlanFactory
     */
    protected $plan;

    /**
     * Constructor
     * @param Session $customer
     * @param PlanFactory $planFactory
     * @param CollectionFactory $planCollection
     * @param ManagepetsFactory $petsFactory
     * @param BreedFactory $breedFactory
     * @param ActivitiesFactory $activitiesFactory
     * @param PriceCurrencyInterface $priceCurrency
     * @param GroomSession $groomingSession
     * @param CityCollection $cities
     * @param StoreManager $storeManager
     * @param Context $context
     * @param CookieManagerInterface $cookieManager
     * @param array $data
     */
    public function __construct(
        Session $customer,
        CollectionFactory $planCollection,
        ManagepetsFactory $petsFactory,
        BreedFactory $breedFactory,
        StoreManager $storeManager,
        ActivitiesFactory $activitiesFactory,
        PriceCurrencyInterface $priceCurrency,
        CityCollection $cities,
        PlanFactory $planFactory,
        GroomSession $groomingSession,
        CookieManagerInterface $cookieManager,
        Context $context,
        array $data = []
    ) {
        $this->customer = $customer;
        $this->plans = $planCollection;
        $this->pets = $petsFactory;
        $this->breed = $breedFactory;
        $this->plan = $planFactory;
        $this->storeManager = $storeManager;
        $this->activities = $activitiesFactory;
        $this->priceCurrency = $priceCurrency;
        $this->cities = $cities;
        $this->groomingSession = $groomingSession;
        $this->cookieManager = $cookieManager;
        parent::__construct($context, $data);
    }

    /**
     * Get Plan collection based on petDetails
     * @return CollectionFactory
     */
    public function getPlans()
    {
        $planCollection = '';
        $petdetails = $this->getPet();
        $city = $this->cookieManager->getCookie('city_screen');
        if (!empty($city) && (strcasecmp($city, 'New delhi') == 0)) {
            $city = "Delhi";
        }
        $cities = $this->cities->create()->addFieldToSelect('cityscreen_id')->addFieldToFilter('city', ['eq' => $city]);
        $citiesArray = [];
        foreach ($cities as $city) {
            $citiesArray[] = ['finset' => $city->getCityscreenId()];
        }

        if ($petdetails) {
            $planCollection = $this->plans->create()
                ->addFieldToFilter('species', $petdetails['type'])
                ->addFieldToFilter('plan_type', ['in' => [2,3]])
                ->addFieldToFilter(
                        ['applicable_cities'],
                        [$citiesArray]
                    )
                ->addFieldToFilter('status', 1)
                ->setOrder('sort_order','ASC');
        }
        return $planCollection;
    }

    /**
     * Get Plan image
     */
    public function getPlanImage($image)
    {
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
        if (!empty($image)) {
            $imageurl = $mediaUrl."plan/feature/".$image;
        } else {
            $imageurl = $mediaUrl.'catalog/product/placeholder/'.$this->getConfig('catalog/placeholder/thumbnail_placeholder');
        }
        return $imageurl;
    }

    public function getConfig($config_path)
    {
        return $this->storeManager->getStore()->getConfig($config_path);
    }

    /**
     * Get Pet Details
     * @return ManagepetsFactory
     */
    public function getPet()
    {
        if (!$this->petdetails) {
            $petData = $this->getRequest()->getPostValue();
            if (!empty($petData['petid'])) {
                $this->petdetails = $this->pets->create()->load($petData['petid'])->getData();
            }
        }
        return $this->petdetails;
    }

    /**
     * Get Breed Type Name
     * @return string
     */
    public function getBreed()
    {
        $breedTypeName = "";
        $petdetails = $this->getPet();
        if ($petdetails) {
            $breedType = $this->breed->create()->load($petdetails['breed'])->getData('breed_type');
            switch ($breedType) {
                case '1':
                    $breedTypeName = "small";
                    break;
                case '2':
                    $breedTypeName = "medium";
                    break;
                case '3':
                    $breedTypeName = "large";
                    break;
                case '4':
                    $breedTypeName = "extra_large";
                    break;
            }
        }
        return $breedTypeName;
    }

    /**
     * Activity details
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
     * @return Grooming Session selected data
     */
    public function getGroomingSession()
    {
        if (!$this->groomSession) {
            $this->groomSession = $this->groomingSession->getGroomCenter();
        }
        return $this->groomSession;
    }

    /**
     * Get Pet by id
     */
    public function getPetById($id)
    {
        if (!empty($id)) {
            $id = $this->pets->create()->load($id)->getName();
        }
        return $id;
    }

    /**
     * Get Plan collection based on petDetails
     * @return CollectionFactory
     */
    public function getPlanById($id)
    {
        $selectedPlan = false;
        if ($id) {
            $plan = $this->plan->create()->load($id);
            if (!empty($plan)) {
                $selectedPlan = $plan;
            }
        }
        
        return $selectedPlan;
    }

    /**
     * Get Breed Type Name
     * @return string
     */
    public function getBreedType($petId)
    {
        $breedTypeName = "";
        $petdetails = $this->pets->create()->load($petId)->getBreed();
        if ($petdetails) {
            $breedType = $this->breed->create()->load($petdetails)->getData('breed_type');
            switch ($breedType) {
                case '1':
                    $breedTypeName = "small";
                    break;
                case '2':
                    $breedTypeName = "medium";
                    break;
                case '3':
                    $breedTypeName = "large";
                    break;
            }
        }
        return $breedTypeName;
    }
}