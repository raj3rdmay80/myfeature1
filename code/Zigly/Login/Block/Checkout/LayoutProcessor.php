<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Login
 */
namespace Zigly\Login\Block\Checkout;

use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory;

class LayoutProcessor
{

    /**
     * @var Collection
     */
    private $collectionFactory;

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface CookieManagerInterface
     */
    private $cookieManager;

    /**
     * @param Session $customerSession
     * @param CollectionFactory $collectionFactory
     * @param CookieManagerInterface $cookieManager
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     */
    public function __construct(
        Session $customerSession,
        CollectionFactory $collectionFactory,
        CookieManagerInterface $cookieManager,
        CustomerRepositoryInterface $customerRepositoryInterface
    ) {

        $this->cookieManager = $cookieManager;
        $this->customerSession = $customerSession;
        $this->collectionFactory = $collectionFactory;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
    }

    /**
     * get the phone number attribute
     *
     *
     * @return integer
     */
    public function getcustomerphonenumber()
    {
        if($this->customerSession->isLoggedIn()) {
            $customerid = $this->customerSession->getCustomerId();
            $customer =$this->customerRepositoryInterface->getById($customerid);
            $phone_number = $customer->getCustomAttribute('phone_number');
            $phone_number = is_null($phone_number) ? "" : $phone_number->getvalue(); 
            return $phone_number;
        }
        
        
    }

    /** Get address Cookie */
    public function getAddressCookie()
    {
        $cookie = [];
        if (!empty($this->cookieManager->getCookie('state'))) {
            $cookie['state'] = $this->cookieManager->getCookie('state');
            $cookie['street1'] = $this->cookieManager->getCookie('street1');
            $cookie['street2'] = $this->cookieManager->getCookie('street2');
            $cookie['pincode_check'] = $this->cookieManager->getCookie('pincode_check');
            $cookie['city_screen'] = $this->cookieManager->getCookie('city_screen');
        }
        return $cookie;
    }

    /**
     * set the phone number and set the placeholder in required fields 
     *
     *
     * @return jslayout
     */
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array  $jsLayout
        ){
        //add phone number value
        $phone_number = $this->getcustomerphonenumber();
        $cookie = $this->getAddressCookie();
        if (!empty($cookie['state'])) {
            $region = $this->collectionFactory->create()
                            ->addRegionNameFilter($cookie['state'])
                            ->getFirstItem()
                            ->toArray();
        }
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['telephone']['value'] =$phone_number;

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['street']['children'][0]['value'] = isset($cookie['street1'])? $cookie['street1'] :'';

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['street']['children'][1]['value'] = isset($cookie['street2'])? $cookie['street2'] :'';

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['city']['value'] = isset($cookie['city_screen'])? $cookie['city_screen'] :'';

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['postcode']['value'] = isset($cookie['pincode_check'])? $cookie['pincode_check'] :'';

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['region_id']['label'] = isset($region['region_id'])? $region['region_id'] :'';

          //add placeholder
          $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['telephone']['placeholder'] = 'Enter Your Mobile Number *';

          $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['firstname']['placeholder'] = 'First Name *';

          $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['street']['children'][0]['placeholder'] = 'Street Address: Line 1 *';

          $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['postcode']['placeholder'] = 'Enter your 6 digit Postal Code *';

          $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['city']['placeholder'] = 'City *';

          $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['region_id']['placeholder'] = 'Please Select.. *';

          // Adding the validation
          if (isset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']))
          {

            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['telephone']['validation'] = ['required-entry' => true, "phonenumber-validation"=>true ];

            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['postcode']['validation'] = ['required-entry' => true, "pincode-validation" => true ];

          }

          // Adding the validation
          if (isset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['before-fields']['children']))
          {

            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['before-fields']['children']['mobilenumber']['validation'] = ['required-entry' => true, "phonenumber-validation"=>true ];
          }


        //Billing Address
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children']))
        {

            foreach ($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'] as $key => $payment) {

                /* Firstname */
                if (isset($payment['children']['form-fields']['children']['firstname'])) {

                    $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children']['firstname']['placeholder'] = 'First Name *'; 
                }

                /* Street Name */
                if (isset($payment['children']['form-fields']['children']['street'])) {

                    $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children']['street']['children'][0]['placeholder'] = 'Street Address: Line 1 *';

                    $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children']['street']['children'][0]['value'] = isset($cookie['street1'])? $cookie['street1'] :'';
                    $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children']['street']['children'][1]['value'] = isset($cookie['street2'])? $cookie['street2'] :'';
                }

                /* region_id */
                if (isset($payment['children']['form-fields']['children']['region_id'])) {

                    $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children']['region_id']['placeholder'] = 'Please Select.. *';
                    $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children']['region_id']['value'] = isset($region['region_id'])? $region['region_id'] :'';
                }

                /* City Name */
                if (isset($payment['children']['form-fields']['children']['city'])) {

                    $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children']['city']['placeholder'] = 'City *';
                    $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children']['city']['value'] = isset($cookie['city_screen'])? $cookie['city_screen'] :'';
                }

                /* Telephone Name */
                if (isset($payment['children']['form-fields']['children']['telephone'])) {

                    $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children']['telephone']['placeholder'] = 'Enter Your Mobile Number *';

                    //Add phone number value
                    $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children']['telephone']['value'] = $phone_number;

                    /* Telephone validation */
                    $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children']['telephone']['validation'] = ['required-entry' => true, "phonenumber-validation"=>true ];

                }

                /* Postcode Name */
                if (isset($payment['children']['form-fields']['children']['postcode'])) {

                    $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children']['postcode']['placeholder'] = 'Enter your 6 digit Postal Code *';
                    $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children']['postcode']['value'] = isset($cookie['pincode_check'])? $cookie['pincode_check'] :'';

                    /* Postcode validation */
                    $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children']['postcode']['validation'] = ['required-entry' => true, "pincode-validation" => true];
                }

        }
        }
              return $jsLayout;
        }
}
