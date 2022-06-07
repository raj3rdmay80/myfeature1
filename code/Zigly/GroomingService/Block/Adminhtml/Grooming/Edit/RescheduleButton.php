<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomingService
 */
declare(strict_types=1);

namespace Zigly\GroomingService\Block\Adminhtml\Grooming\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Framework\AuthorizationInterface;
use Zigly\GroomingService\Model\GroomingFactory;
use Zigly\GroomingService\Helper\ServiceStatus;

class RescheduleButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param AuthorizationInterface $authorization
     * @param \Magento\Framework\Registry $registry
     * @param GroomingFactory $groomingFactory
     * @param ServiceStatus $serviceStatus
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        AuthorizationInterface $authorization,
        GroomingFactory $groomingFactory,
        ServiceStatus $serviceStatus
    ) {
        $this->authorization = $authorization;
        $this->groomingFactory = $groomingFactory;
        $this->serviceStatus = $serviceStatus;
        parent::__construct($context);
    }


    /**
     * @return array
     */
    public function getButtonData()
    {
        if (!$this->authorization->isAllowed('Zigly_GroomingService::Grooming_reschedule_service')) {
            return [];
        }
        $service = $this->groomingFactory->create()->load($this->getModelId());
        $reschedulableStatus = $this->serviceStatus->getReschedulableStatus();
        $now = new \DateTime('now', new \DateTimeZone('Asia/Kolkata'));
        $currentTimeStamp = $now->getTimestamp() + $now->getOffset();
        if(in_array($service->getBookingStatus(), $reschedulableStatus) && ($currentTimeStamp > $service->getScheduledTimestamp() || $service->getGroomerId())) {
            return [
                'label' => __('Reschedule'),
                'class' => 'reschedule',
                'on_click' => sprintf("location.href = '%s';", $this->getRescheduleUrl()),
                'sort_order' => 10

            ];
        }
        return [];
    }

    /**
     * Get URL for edit
     *
     * @return string
     */
    public function getRescheduleUrl()
    {
        return $this->getUrl('zigly_groomingservice/grooming/reschedule', ['service_id' => $this->getModelId()]);
    }
}
