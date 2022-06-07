<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Zigly\GroomingService\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Zigly\GroomingService\Model\Grooming;

class ServiceStatus extends AbstractHelper
{
    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
    }

    /**
     * Return all Service Status
     *
     * @return  array
     */
    public function getServiceStatus()
    {
        $serviceStatus = [
            Grooming::STATUS_PENDING => Grooming::STATUS_PENDING,
            Grooming::STATUS_SCHEDULED => Grooming::STATUS_SCHEDULED,
            Grooming::STATUS_COMPLETED => Grooming::STATUS_COMPLETED,
            Grooming::STATUS_INPROGRESS => Grooming::STATUS_INPROGRESS,
            Grooming::STATUS_CANCELLED_BY_ADMIN => Grooming::STATUS_CANCELLED_BY_ADMIN,
            Grooming::STATUS_CANCELLED_BY_CUSTOMER => Grooming::STATUS_CANCELLED_BY_CUSTOMER,
            Grooming::STATUS_CANCELLED_BY_PROFESSIONAL => Grooming::STATUS_CANCELLED_BY_PROFESSIONAL,
            Grooming::STATUS_RESCHEDULED_BY_ADMIN => Grooming::STATUS_RESCHEDULED_BY_ADMIN,
            Grooming::STATUS_RESCHEDULED_BY_CUSTOMER => Grooming::STATUS_RESCHEDULED_BY_CUSTOMER,
            Grooming::STATUS_RESCHEDULED_BY_PROFESSIONAL => Grooming::STATUS_RESCHEDULED_BY_PROFESSIONAL,
            Grooming::STATUS_CUSTOMER_NOT_REACHABLE => Grooming::STATUS_CUSTOMER_NOT_REACHABLE,
            Grooming::STATUS_CAN_T_DELIVER_SERVICE => Grooming::STATUS_CAN_T_DELIVER_SERVICE,
            Grooming::STATUS_I_HAVE_ARRIVED => Grooming::STATUS_I_HAVE_ARRIVED,
        ];
        return $serviceStatus;
    }

    /**
     * Cancellable Status
     *
     * @return  array
     */
    public function getCancellableStatus() {
        return [Grooming::STATUS_PENDING, Grooming::STATUS_SCHEDULED, Grooming::STATUS_RESCHEDULED_BY_ADMIN, Grooming::STATUS_RESCHEDULED_BY_CUSTOMER, Grooming::STATUS_RESCHEDULED_BY_PROFESSIONAL];
    }

    /**
     * Cancel Status
     *
     * @return  array
     */
    public function getCancelStatus() {
        return [Grooming::STATUS_CANCELLED_BY_ADMIN, Grooming::STATUS_CANCELLED_BY_CUSTOMER, Grooming::STATUS_CANCELLED_BY_PROFESSIONAL];
    }

    /**
     * Reschedulable Status
     *
     * @return  array
     */
    public function getReschedulableStatus() {
        return [Grooming::STATUS_SCHEDULED, Grooming::STATUS_RESCHEDULED_BY_ADMIN, Grooming::STATUS_RESCHEDULED_BY_CUSTOMER, Grooming::STATUS_RESCHEDULED_BY_PROFESSIONAL];
    }

    /**
     * Reassignable Status
     *
     * @return  array
     */
    public function getNotReassignableStatus() {
        return [Grooming::STATUS_COMPLETED, Grooming::STATUS_INPROGRESS];
    }
}
