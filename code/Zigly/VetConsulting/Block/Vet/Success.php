<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_VetConsulting
 */
declare(strict_types=1);

namespace Zigly\VetConsulting\Block\Vet;

use Magento\Framework\View\Element\Template\Context;
use Zigly\VetConsulting\Model\Session as VetSession;
use Zigly\GroomingService\Model\GroomingFactory;

/**
 * Success
 */
class Success extends \Magento\Framework\View\Element\Template
{

    /**
     * @var VetSession
     */
    protected $vetSession;

    /**
     * Constructor
     * @param VetSession $vetSession
     * @param array $data
     */
    public function __construct(
        VetSession $vetSession,
        GroomingFactory $groomingFactory,
        Context $context,
        array $data = []
    ) {
        $this->vetSession = $vetSession;
        $this->groomingFactory = $groomingFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return Grooming Session selected data
     */
    public function getVetPet()
    {
        $paramData = $this->getRequest()->getParams();

        $bookingId = 0;
        if (!empty($this->vetSession->getVet()['booking_id'])) {
            $bookingId = $this->vetSession->getVet()['booking_id'];
            $this->vetSession->setVet([]);
        }  else if (!empty($paramData['id'])) {
            $bookingId = $paramData['id'];
        }
        if ($bookingId) {
            $bookingDetails = $this->groomingFactory->create()->load($bookingId);
            $name = !empty($bookingDetails->getPetName()) ? $bookingDetails->getPetName() : '';
            return $name;
        }
        return false;
    }
}