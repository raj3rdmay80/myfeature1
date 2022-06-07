<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomingService
 */
declare(strict_types=1);

namespace Zigly\GroomingService\Block\Adminhtml\Grooming\Edit;

use Zigly\GroomingService\Model\GroomingFactory;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class RescheduleBackButton extends GenericButton implements ButtonProviderInterface
{

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param GroomingFactory $groomingFactory
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        GroomingFactory $groomingFactory
    ) {
        $this->groomingFactory = $groomingFactory;
        parent::__construct($context);
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Back'),
            'on_click' => sprintf("location.href = '%s';", $this->getBackUrl()),
            'class' => 'back',
            'sort_order' => 10
        ];
    }

    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('zigly_groomingservice/grooming/view', ['entity_id' => $this->context->getRequest()->getParam('service_id')]);
    }
}

