<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomingService
 */
declare(strict_types=1);

namespace Zigly\GroomingService\Block\ExperienceGrooming;

use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\CustomerFactory;
use Magento\Directory\Model\CurrencyFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Directory\Model\RegionFactory;
use Magento\Customer\Model\AddressFactory;

class AddressForm extends \Magento\Framework\View\Element\Template
{

    /**
     * @var RegionFactory
     */
    protected $regionFactory;

    /**
     * @var AddressFactory
     */
    protected $address;

    /**
     * Constructor
     * @param Context $context
     * @param RegionFactory $regionFactory
     * @param AddressFactory $address
     * @param array $data
     */
    public function __construct(
        Context $context,
        RegionFactory $regionFactory,
        AddressFactory $address,
        array $data = []
    ) {
        $this->regionFactory = $regionFactory;
        $this->address = $address;
        parent::__construct($context, $data);
    }

    public function getRegionCollection()
    {
        $regions = $this->regionFactory->create()->getCollection()->addFieldToFilter('country_id','IN')->getData();
        return $regions;
    }

    public function getEditAddress()
    {
        $address = false;
        if ($addressId = $this->getRequest()->getParam('id')) {
            $shippingAddress = $this->address->create()->load($addressId);
            if (!empty($shippingAddress)) {
                $address = $shippingAddress;
            }
        }
        return $address;
    }

}