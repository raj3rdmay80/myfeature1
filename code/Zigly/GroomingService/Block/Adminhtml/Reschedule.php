<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomingService
 */
declare(strict_types=1);

namespace Zigly\GroomingService\Block\Adminhtml;

use Magento\Backend\Block\Widget\Context;
use Zigly\GroomingService\Model\GroomingFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Reschedule extends \Magento\Backend\Block\Template
{
    /**
 	* Block template.
 	*
 	* @var string
 	*/
    protected $_template = 'reschedule.phtml';

    /**
    * @var ScopeConfigInterface
    */
    protected $scopeConfig;

    /**
     * Constructor
     * @param Context $context
     * @param GroomingFactory $groomingFactory
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        GroomingFactory $groomingFactory,
        Context $context
    ) {
        $this->context = $context;
        $this->scopeConfig = $scopeConfig;
        $this->groomingFactory = $groomingFactory;
        parent::__construct($context);
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
     * get grooming service
     */
    public function getGroomingService()
    {
        $id = $this->context->getRequest()->getParam('service_id');
        $service = $this->groomingFactory->create()->load($id);
        return $service;
    }

    /*
    * Get Start Hours
    */
    public function getStartHr()
    {
        $center = $this->getGroomingService()->getCenter();
        if ($center == "At Home") {
            return $this->getConfig('zigly_timeslot/start/hours');
        }
        return $this->getConfig('zigly_timeslot_experience/start/hours');
    }

    /*
    * Get Start Minutes
    */
    public function getStartMin()
    {
        $center = $this->getGroomingService()->getCenter();
        if ($center == "At Home") {
            return $this->getConfig('zigly_timeslot/start/minutes');
        }
        return $this->getConfig('zigly_timeslot_experience/start/minutes');
    }

    /*
    * Get End Hrs
    */
    public function getEndHr()
    {
        $center = $this->getGroomingService()->getCenter();
        if ($center == "At Home") {
            return $this->getConfig('zigly_timeslot/end/hours');
        }
        return $this->getConfig('zigly_timeslot_experience/end/hours');
    }

    /*
    * Get End Minutes
    */
    public function getEndMin()
    {
        $center = $this->getGroomingService()->getCenter();
        if ($center == "At Home") {
            return $this->getConfig('zigly_timeslot/end/minutes');
        }
        return $this->getConfig('zigly_timeslot_experience/end/minutes');
    }


}
