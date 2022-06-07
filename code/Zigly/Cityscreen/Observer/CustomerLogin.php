<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Cityscreen
 */
declare(strict_types=1);

namespace Zigly\Cityscreen\Observer;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Customer\Model\CustomerFactory;

class CustomerLogin implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface CookieManagerInterface
     */
    private $cookieManager;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory CookieMetadataFactory
     */
    private $cookieMetadataFactory;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @param CookieManagerInterface $cookieManager
     * @param CustomerFactory $customerFactory
     * @param CookieMetadataFactory $cookieMetadataFactory
     */
    public function __construct(
        CookieManagerInterface $cookieManager,
        CustomerFactory $customerFactory,
        CookieMetadataFactory $cookieMetadataFactory

    ) {
        $this->cookieManager = $cookieManager;
        $this->customerFactory = $customerFactory;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
    }

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {

        try {
            $customer = $observer->getEvent()->getCustomer();
            if ($customer && $this->cookieManager->getCookie('glatlng')) {
                $customer = $this->customerFactory->create()->load($customer->getEntityId());
                $customerData = $customer->getDataModel();
                $customerData->setCustomAttribute('latlng', "app".$this->cookieManager->getCookie('glatlng'));
                $customer->updateData($customerData);
                $this->customerFactory->create()->getResource()->saveAttribute($customer, 'latlng');
            }
        } catch (\Exception $e) {
            /*print_r($e->getMessage());*/
        }
    }
}
