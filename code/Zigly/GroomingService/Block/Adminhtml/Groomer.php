<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomingService
 */
declare(strict_types=1);

namespace Zigly\GroomingService\Block\Adminhtml;

use Magento\Backend\Block\Widget\Context;

class Groomer extends \Magento\Backend\Block\Template {

    /**
     * @var $context
     */
    protected $context;

    /**
     * @param array $data
     * @param Context $context
     */
    public function __construct(
        Context $context,
        array $data = []
    ) {
        $this->context = $context;
        parent::__construct($context, $data);
    }

    /**
     * Return entity ID
     *
     * @return int|null
     */
    public function getServiceId()
    {
        return $this->context->getRequest()->getParam('entity_id');
    }
}