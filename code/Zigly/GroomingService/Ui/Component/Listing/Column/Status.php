<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomingService
 */
declare(strict_types=1);

namespace Zigly\GroomingService\Ui\Component\Listing\Column;

use Zigly\GroomingService\Model\Grooming;

class Status implements \Magento\Framework\Option\ArrayInterface
{

    public function toOptionArray()
    {
        return [
            ['value' => Grooming::STATUS_PENDING, 'label' => Grooming::STATUS_PENDING],
            ['value' => Grooming::STATUS_SCHEDULED, 'label' => Grooming::STATUS_SCHEDULED],
            ['value' => Grooming::STATUS_COMPLETED, 'label' => Grooming::STATUS_COMPLETED],
            ['value' => Grooming::STATUS_INPROGRESS, 'label' => Grooming::STATUS_INPROGRESS],
            ['value' => Grooming::STATUS_CANCELLED_BY_ADMIN, 'label' => Grooming::STATUS_CANCELLED_BY_ADMIN],
            ['value' => Grooming::STATUS_CANCELLED_BY_CUSTOMER, 'label' => Grooming::STATUS_CANCELLED_BY_CUSTOMER],
            ['value' => Grooming::STATUS_CANCELLED_BY_PROFESSIONAL, 'label' => Grooming::STATUS_CANCELLED_BY_PROFESSIONAL],
            ['value' => Grooming::STATUS_RESCHEDULED_BY_ADMIN, 'label' => Grooming::STATUS_RESCHEDULED_BY_ADMIN],
            ['value' => Grooming::STATUS_RESCHEDULED_BY_CUSTOMER, 'label' => Grooming::STATUS_RESCHEDULED_BY_CUSTOMER],
            ['value' => Grooming::STATUS_RESCHEDULED_BY_PROFESSIONAL, 'label' => Grooming::STATUS_RESCHEDULED_BY_PROFESSIONAL],
            ['value' => Grooming::STATUS_CUSTOMER_NOT_REACHABLE, 'label' => Grooming::STATUS_CUSTOMER_NOT_REACHABLE],
            ['value' => Grooming::STATUS_CAN_T_DELIVER_SERVICE, 'label' => Grooming::STATUS_CAN_T_DELIVER_SERVICE],
            ['value' => Grooming::STATUS_I_HAVE_ARRIVED, 'label' => Grooming::STATUS_I_HAVE_ARRIVED]
        ];
    }
}