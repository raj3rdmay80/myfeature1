<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomingService
 */
declare(strict_types=1);

namespace Zigly\GroomingService\Plugin;

use Magento\Sales\Model\OrderFactory;
use Zigly\GroomingService\Model\Grooming;
use Zigly\GroomingService\Helper\ServiceStatus;
use Zigly\GroomingService\Model\GroomingFactory;
use Magento\Sales\Api\OrderItemRepositoryInterface;

class AfterServiceSave
{

    /** @var ServiceStatus */
    protected $serviceStatus;

    /**
     * @param OrderFactory $orderFactory
     * @param ServiceStatus $serviceStatus
     * @param GroomingFactory $groomingFactory
     * @param OrderItemRepositoryInterface $itemsRepository
     */
    public function __construct(
        OrderFactory $orderFactory,
        ServiceStatus $serviceStatus,
        GroomingFactory $groomingFactory,
        OrderItemRepositoryInterface $itemsRepository
    ) {
        $this->orderFactory = $orderFactory;
        $this->serviceStatus = $serviceStatus;
        $this->itemsRepository = $itemsRepository;
        $this->groomingFactory = $groomingFactory;
    }

    public function afterSave(Grooming $subject, $result)
    {
        $service = $this->groomingFactory->create()->load($subject->getBookingId());
        $orderData = $this->orderFactory->create()->getCollection()->addFieldToFilter('booking_id',$subject->getBookingId())->getFirstItem();
        if($orderData->getEntityId()){
            $order = $this->orderFactory->create()->load($orderData->getEntityId());
            $cancelStatus = $this->serviceStatus->getCancelStatus();
            if (in_array($service->getBookingStatus(), $cancelStatus)) {
                $state = "canceled";
                $status = strtolower($service->getBookingStatus());
                $status = str_replace(" ","_",$status);
                $order->setStatus($status)->setState($state);
                $itemcancelled = [];
                $orderItems = $order->getAllItems();
                foreach ($orderItems as $items) {
                    $id = $items->getId();
                    $item = $this->itemsRepository->get($id);
                    $qty = $item->getQtyOrdered();
                    $item->setQtyCanceled($qty)->save();
                    $itemcancelled[] = $item;
                }
                $order->setItems($itemcancelled);
            }
            $completedStatus = [Grooming::STATUS_COMPLETED];
            if (in_array($service->getBookingStatus(), $completedStatus)) {
                $state = "complete";
                $order->setStatus($state)->setState($state);
            }
            $newStatus = [Grooming::STATUS_PENDING];
            if (in_array($service->getBookingStatus(), $newStatus)) {
                $state = "new";
                $status = "pending";
                $order->setStatus($status)->setState($state);
            }
            $processingStatus = [Grooming::STATUS_SCHEDULED, Grooming::STATUS_RESCHEDULED_BY_ADMIN, Grooming::STATUS_RESCHEDULED_BY_CUSTOMER, Grooming::STATUS_RESCHEDULED_BY_PROFESSIONAL, Grooming::STATUS_I_HAVE_ARRIVED, Grooming::STATUS_CAN_T_DELIVER_SERVICE, Grooming::STATUS_CUSTOMER_NOT_REACHABLE, Grooming::STATUS_INPROGRESS];
            if (in_array($service->getBookingStatus(), $processingStatus)) {
                $state = "processing";
                $status = strtolower($service->getBookingStatus());
                $status = str_replace(" ","_",$status);
                $order->setStatus($status)->setState($state);
            }
            $order->save();
        }
        return $result;
    }

}