<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_VetConsultingApi
 */
declare(strict_types=1);

namespace Zigly\VetConsultingApi\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Zigly\VetConsultingApi\Api\VetConsultingTimeSlotsRepositoryInterface;

class VetConsultingTimeSlotsRepository implements VetConsultingTimeSlotsRepositoryInterface
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Initialize service
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function getTimeSlotsVetConsulting()
    {
        try{
            $start_hours = $this->scopeConfig->getValue('zigly_timeslot_vetconsulting/start/hours', ScopeInterface::SCOPE_STORE);
            $start_minutes = $this->scopeConfig->getValue('zigly_timeslot_vetconsulting/start/minutes', ScopeInterface::SCOPE_STORE);
            $end_hours = $this->scopeConfig->getValue('zigly_timeslot_vetconsulting/end/hours', ScopeInterface::SCOPE_STORE);
            $end_minutes = $this->scopeConfig->getValue('zigly_timeslot_vetconsulting/end/minutes', ScopeInterface::SCOPE_STORE);
            $startHours = $start_hours.':'.$start_minutes;
            $endHours = $end_hours.':'.$end_minutes;
            $response = new \Magento\Framework\DataObject();
            $response->setStartHours($startHours);
            $response->setEndHours($endHours);
            return $response;
         } catch (\Exception $e) {
            throw new NoSuchEntityException(__($e->getMessage()), $e);
        }
    }
}