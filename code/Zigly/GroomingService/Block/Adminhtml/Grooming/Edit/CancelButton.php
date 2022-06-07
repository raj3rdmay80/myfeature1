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
use Magento\Store\Model\StoreManagerInterface;
use Zigly\GroomingService\Helper\ServiceStatus;

class CancelButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param AuthorizationInterface $authorization
     * @param StoreManagerInterface $storeManager
     * @param \Magento\Framework\Registry $registry
     * @param GroomingFactory $groomingFactory
     * @param ServiceStatus $serviceStatus
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        AuthorizationInterface $authorization,
        StoreManagerInterface $storeManager,
        GroomingFactory $groomingFactory,
        ServiceStatus $serviceStatus
    ) {
        $this->authorization = $authorization;
        $this->storeManager = $storeManager;
        $this->groomingFactory = $groomingFactory;
        $this->serviceStatus = $serviceStatus;
        parent::__construct($context);
    }


    /**
     * @return array
     */
    public function getButtonData()
    {
        if (!$this->authorization->isAllowed('Zigly_GroomingService::Grooming_cancel_service')) {
            return [];
        }
        $service = $this->groomingFactory->create()->load($this->getModelId());
        $cancellableStatus = $this->serviceStatus->getCancellableStatus();
        $now = new \DateTime('now', new \DateTimeZone('Asia/Kolkata'));
        $now->add(new \DateInterval('PT30M'));
        $currentTimeStamp = $now->getTimestamp() + $now->getOffset();
        $url = $this->storeManager->getStore()->getBaseUrl();
        $policy = $url.'cancellation-return-exchange-and-refund-policy';
        if(in_array($service->getBookingStatus(), $cancellableStatus) && $service->getGroomerId()  && $currentTimeStamp < $service->getScheduledTimestamp()) {
            return [
                'label' => __('Cancel'),
                'class' => 'cancel',
                'on_click' => 'deleteConfirm(\'' . __(
                    'By submitting the request, you agree the <a href="'.$policy.'" target="_blank">cancellation policies</a>'
                ) . '\', \'' . $this->getCancelUrl() . '\')',
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
    public function getCancelUrl()
    {
        return $this->getUrl('zigly_groomingservice/grooming/cancel', ['service_id' => $this->getModelId()]);
    }
}
