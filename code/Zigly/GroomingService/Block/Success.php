<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomingService
 */
declare(strict_types=1);

namespace Zigly\GroomingService\Block;

use Magento\Framework\View\Element\Template\Context;
use Zigly\GroomingService\Model\Session as GroomSession;
use Zigly\GroomingService\Model\GroomingFactory;

/**
 * Success
 */
class Success extends \Magento\Framework\View\Element\Template
{

    /**
     * @var GroomSession
     */
    protected $groomingSession;

    /**
     * Constructor
     * @param GroomSession $groomingSession
     * @param GroomingFactory $groomingFactory
     * @param array $data
     */
    public function __construct(
        GroomSession $groomingSession,
        GroomingFactory $groomingFactory,
        Context $context,
        array $data = []
    ) {
        $this->groomingSession = $groomingSession;
        $this->groomingFactory = $groomingFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return Grooming Session selected data
     */
    public function getGroomingPet()
    {
        $groomSession = $this->groomingSession->getGroomService();

        $paramData = $this->getRequest()->getParams();
        
        $bookingId = 0;
        if (!empty($groomSession['booking_id'])) {
            $bookingId = $groomSession['booking_id'];
            $this->groomingSession->setGroomService([]);
        } else if (!empty($this->groomingSession->getGroomCenter()['booking_id'])) {
            $bookingId = $this->groomingSession->getGroomCenter()['booking_id'];
            $this->groomingSession->setGroomCenter([]);
        } else if (!empty($paramData['id'])) {
            $bookingId = $paramData['id'];
        }
        $bookingDetails = $this->groomingFactory->create()->load($bookingId);

        return $bookingDetails->getPetName();
    }

    public function isAtHome()
    {
        $groomSession = $this->groomingSession->getGroomService();

        $paramData = $this->getRequest()->getParams();
        $bookingId = 0;
        if (!empty($groomSession['booking_id'])) {
            $bookingId = $groomSession['booking_id'];
            $this->groomingSession->setGroomService([]);
        } else if (!empty($this->groomingSession->getGroomCenter()['booking_id'])) {
            $bookingId = $this->groomingSession->getGroomCenter()['booking_id'];
            $this->groomingSession->setGroomCenter([]);
        } else if (!empty($paramData['id'])) {
            $bookingId = $paramData['id'];
        }
        $bookingDetails = $this->groomingFactory->create()->load($bookingId);
        if ($bookingDetails->getBookingType() == 1 && $bookingDetails->getCenter() == "At Home") {
            return $bookingId;
        } else {
            return false;
        }
    }
}