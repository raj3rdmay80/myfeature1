<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Sales
 */
declare(strict_types=1);

namespace Zigly\Sales\Block\Booking;

use Zigly\GroomingService\Model\GroomingFactory;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Zigly\Groomer\Model\GroomerFactory as ProfessionalFactory;

class Reschedule extends \Magento\Framework\View\Element\Template
{

    /**
     * @var GroomingFactory
     */
    protected $groomingFactory;

    /**
     * @var TimezoneInterface
     */
    protected $timezoneInterface;

    /**
    * @var ScopeConfigInterface
    */
    protected $scopeConfig;

    /**
     * Constructor
     * @param Context $context
     * @param GroomingFactory $groomingFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param TimezoneInterface $timezoneInterface
     * @param array $data
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        GroomingFactory $groomingFactory,
        TimezoneInterface $timezoneInterface,
        array $data = []
    ) {
        $this->groomingFactory = $groomingFactory;
        $this->scopeConfig = $scopeConfig;
        $this->timezoneInterface = $timezoneInterface;
        parent::__construct($context, $data);
    }

    /*
    * set date format
    */
    public function getDate($date)
    {
        return $this->timezoneInterface->date(new \DateTime($date))->format('d M \'y');
    }

    /*
    * get booking details by id
    */
    public function getBookingDetails()
    {
        $bookingId = $this->getRequest()->getParam('id');
        $bookingDetails = $this->groomingFactory->create()->load($bookingId);
        return $bookingDetails;
    }

    /*
    * get professional by id
    */
    public function getProfessionalById($id)
    {
        $professional = $this->professionalFactory->create()->load($id);
        return $professional;
    }

    /*
    * Get Config
    */
    public function getConfig($path)
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue($path, $storeScope);
    }

    /*
    * Get Start Hours
    */
    public function getStartHr()
    {
        $center = $this->getBookingDetails()->getCenter();
        if ($center == "At Home") {
            return $this->getConfig('zigly_timeslot/start/hours');
        } elseif ($this->getBookingDetails()->getBookingType() == 2) {
            return $this->getConfig('zigly_timeslot_vetconsulting/start/hours');
        }
        return $this->getConfig('zigly_timeslot_experience/start/hours');
    }

    /*
    * Get Start Minutes
    */
    public function getStartMin()
    {
        $center = $this->getBookingDetails()->getCenter();
        if ($center == "At Home") {
            return $this->getConfig('zigly_timeslot/start/minutes');
        } elseif ($this->getBookingDetails()->getBookingType() == 2) {
            return $this->getConfig('zigly_timeslot_vetconsulting/start/minutes');
        }
        return $this->getConfig('zigly_timeslot_experience/start/minutes');
    }

    /*
    * Get End Hrs
    */
    public function getEndHr()
    {
        $center = $this->getBookingDetails()->getCenter();
        if ($center == "At Home") {
            return $this->getConfig('zigly_timeslot/end/hours');
        } elseif ($this->getBookingDetails()->getBookingType() == 2) {
            return $this->getConfig('zigly_timeslot_vetconsulting/end/hours');
        }
        return $this->getConfig('zigly_timeslot_experience/end/hours');
    }

    /*
    * Get End Minutes
    */
    public function getEndMin()
    {
        $center = $this->getBookingDetails()->getCenter();
        if ($center == "At Home") {
            return $this->getConfig('zigly_timeslot/end/minutes');
        } elseif ($this->getBookingDetails()->getBookingType() == 2) {
            return $this->getConfig('zigly_timeslot_vetconsulting/end/minutes');
        }
        return $this->getConfig('zigly_timeslot_experience/end/minutes');
    }
}