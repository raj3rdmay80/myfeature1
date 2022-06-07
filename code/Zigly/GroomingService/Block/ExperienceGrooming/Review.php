<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomingService
 */
declare(strict_types=1);

namespace Zigly\GroomingService\Block\ExperienceGrooming;

use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Customer\Model\Customer;
use Zigly\Managepets\Model\ManagepetsFactory;
use Magento\Store\Model\StoreManager;
use Zigly\Species\Model\BreedFactory;
use Zigly\Activities\Model\ActivitiesFactory;
use Zigly\Plan\Model\PlanFactory;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Zigly\Hub\Model\HubFactory;
use Zigly\GroomingService\Model\Session as GroomSession;

/**
 * Review all selected details
 */
class Review extends \Magento\Framework\View\Element\Template
{

    /**
     * @var PlanFactory
     */
    protected $plan;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

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
     * @var HubFactory
     */
    protected $hub;

    /**
     * @var GroomSession
     */
    protected $groomingSession;

    /**
     * @var groom session's value
     */
    protected $groomSession;

    /**
     * Constructor
     * @param Customer $customer
     * @param CustomerSession $customerSession
     * @param PlanFactory $planFactory
     * @param ManagepetsFactory $petsFactory
     * @param BreedFactory $breedFactory
     * @param ActivitiesFactory $activitiesFactory
     * @param PriceCurrencyInterface $priceCurrency,
     * @param Context $context
     * @param HubFactory $hub
     * @param StoreManager $storeManager
     * @param GroomSession $groomingSession
     * @param array $data
     */
    public function __construct(
        CustomerSession $customerSession,
        PlanFactory $planFactory,
        Customer $customer,
        ManagepetsFactory $petsFactory,
        BreedFactory $breedFactory,
        StoreManager $storeManager,
        ActivitiesFactory $activitiesFactory,
        PriceCurrencyInterface $priceCurrency,
        HubFactory $hub,
        GroomSession $groomingSession,
        Context $context,
        array $data = []
    ) {
        $this->customer = $customer;
        $this->customerSession = $customerSession;
        $this->plan = $planFactory;
        $this->pets = $petsFactory;
        $this->breed = $breedFactory;
        $this->storeManager = $storeManager;
        $this->activities = $activitiesFactory;
        $this->priceCurrency = $priceCurrency;
        $this->hub = $hub;
        $this->groomingSession = $groomingSession;
        parent::__construct($context, $data);
    }

    /**
     * Get Plan collection based on petDetails
     * @return CollectionFactory
     */
    public function getPlans()
    {
        $selectedPlan = false;
        if ($planid = $this->getGroomingSession()['planid']) {
            $plan = $this->plan->create()->load($planid);
            if (!empty($plan)) {
                $selectedPlan = $plan;
            }
        }
        
        return $selectedPlan;
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

    public function getDate()
    {
        if ($date = $this->getGroomingSession()['selected_date']) {
            return \DateTime::createFromFormat('Y-m-d', $date)->format('d M \'y');
        }
        return '';
    }

    public function  getWalletBalance()
    {
        $customerId = $this->customerSession->getCustomerId();
        $customerModel = $this->customer->load($customerId);
        $totalBalance = is_null($customerModel->getWalletBalance()) ? "0" : $customerModel->getWalletBalance();
        return $totalBalance;
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
     * Get Pet Details
     * @return ManagepetsFactory
     */
    public function getPet()
    {
        $petdetails = "";
        if ($petId = $this->getGroomingSession()['pet_id']) {
            $petdetails = $this->pets->create()->load($petId)->getData();
        }
        return $petdetails;
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
     * @return address string
     */
    public function getAddressDetails()
    {
        $address = false;

        if ($addressId = $this->getGroomingSession()['address_id']) {
            $shippingAddress = $this->hub->create()->load($addressId);
            if (!empty($shippingAddress)) {
                $address = $shippingAddress->getStreetOne().", ";
                if (!empty($shippingAddress->getStreetTwo())) {
                    $address .= $shippingAddress->getStreetTwo().", ";
                }
                $address .= $shippingAddress->getCity().", ".$shippingAddress->getState()." - ". $shippingAddress->getPincode();
                if ($shippingAddress->getAvailabilityInfo()) {
                    $address .= "<br>".$shippingAddress->getAvailabilityInfo();
                }

            }
        }
        return $address;
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
     * @return Update Grooming Session selected data
     */
    public function updateGroomingSession($groomSession)
    {
        $this->groomingSession->setGroomCenter($groomSession);
        return;
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
     * Get currency symbol for current locale and currency code
     *
     * @return string
     */    
    public function getCurrentCurrencySymbol()
    {
        $currencySymbol = $this->priceCurrency->getCurrencySymbol('default');
        return $currencySymbol;
    } 
}
