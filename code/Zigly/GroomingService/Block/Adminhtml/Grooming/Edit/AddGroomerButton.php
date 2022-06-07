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

class AddGroomerButton extends GenericButton implements ButtonProviderInterface
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
        if (!$this->authorization->isAllowed('Zigly_GroomingService::Grooming_add_grommer')) {
            return [];
        }
        $service = $this->groomingFactory->create()->load($this->getModelId());
        $reassignableStatus = $this->serviceStatus->getNotReassignableStatus();
        $label = __('Add Groomer');
        if ($service->getGroomerId()) {
            $label = __('Reassign Groomer');
        }
        $now = new \DateTime('now', new \DateTimeZone('Asia/Kolkata'));
        $currentTimeStamp = $now->getTimestamp() + $now->getOffset();
        if(!in_array($service->getBookingStatus(), $reassignableStatus) && $currentTimeStamp < $service->getScheduledTimestamp()) {
            return [
                'label' => $label,
                'class' => 'open-insert-listing-example-modal-button',
                'on_click' => sprintf("location.href = '%s';", $this->getAddGroomeUrl()),
                'sort_order' => 20

            ];
        }
        return [];
    }

    /**
     * Get URL for edit
     *
     * @return string
     */
    public function getAddGroomeUrl()
    {
        return $this->getUrl('zigly_groomingservice/grooming/addgroomer', ['entity_id' => $this->getModelId()]);
    }
}
