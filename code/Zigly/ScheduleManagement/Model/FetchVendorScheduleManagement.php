<?php
/**
 * Copyright © Zigly All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Zigly\ScheduleManagement\Model;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Zigly\ProfessionalGraphQl\Helper\Encryption;

class FetchVendorScheduleManagement implements \Zigly\ScheduleManagement\Api\FetchVendorScheduleManagementInterface
{
    public function __construct(
        \Zigly\ScheduleManagement\Model\Data\ScheduleResponse $scheduleResponse,
        \Zigly\ScheduleManagement\Model\Data\ScheduleFactory $scheduleFactory,
        \Zigly\ProfessionalGraphQl\Helper\Encryption $encryption,
        \Zigly\ScheduleManagement\Model\ResourceModel\ScheduleManagement\Collection $resourceCollection
    ) {
        $this->collection = $resourceCollection;
        $this->scheduleResponse = $scheduleResponse;
        $this->encryption = $encryption;
        $this->scheduleFactory = $scheduleFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getFetchVendorSchedule($date, $token)
    {
        $professionalExists = $this->encryption->tokenAuthentication($token);
        if (!$professionalExists) {
            throw new NoSuchEntityException(__('Invalid token'));
        }
        $professional_id = $professionalExists->getGroomerId();
        $result = $this->collection->addFieldToFilter('schedule_date', $date)
            ->addFieldToFilter('professional_id', $professional_id)
            ->setOrder('slot_start_time', 'ASC')
            ->getData();

        if (!count($result)) {
			throw new NoSuchEntityException(__('No schedule found'));
        } else {
            $scheduleData = [];
            $working_mode = '';
            foreach ($result as $slot) {
                $Schedule = $this->scheduleFactory->create();
                $Schedule->setSlot($slot['slot'])
                    ->setBookingId($slot['booking_id'])
                    ->setAvailability($slot['availability']);
                $scheduleData[] = $Schedule;
                $working_mode = $slot['working_mode'];
            }
            $this->scheduleResponse->setProfessionalId($professional_id)
                ->setScheduleDate($date)
                ->setWorkingMode($working_mode)
                ->setSchedule($scheduleData);
        }

        return $this->scheduleResponse;
    }
}
