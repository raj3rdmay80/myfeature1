<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_VetConsulting
 */
declare(strict_types=1);

namespace Zigly\VetConsulting\Block\Adminhtml;

use Zigly\GroomingService\Model\GroomingFactory;

class Grid extends \Magento\Backend\Block\Widget\Grid
{

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param GroomingFactory $groomingFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        GroomingFactory $groomingFactory,
        array $data = []
    ) {
        $this->groomingFactory = $groomingFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    public function _construct()
    {
        parent::_construct();
        $bookingId = $this->getRequest()->getParam('entity_id');
        $service = $this->groomingFactory->create()->load($bookingId);
        if (!empty($service->getScheduledDate()) && !empty($service->getScheduledTime())) {
            $this->getCollection()->getSelect()->join(['scheduled'=>'zigly_schedulemanagement_schedulemanagement'],"(main_table.groomer_id = scheduled.professional_id AND scheduled.booking_id = 0 AND scheduled.availability = 1 )",['schedulemanagement_id' => 'scheduled.schedulemanagement_id']);
            $gmtTimezone = new \DateTimeZone('GMT');
            $myDateTime = new \DateTime($service->getScheduledDate()." ".$service->getScheduledTime(), $gmtTimezone);
            $this->getCollection()->addFieldToFilter('scheduled.slot_start_time', ['eq' => $myDateTime]);
        }
    }
}