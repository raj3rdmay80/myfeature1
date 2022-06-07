<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Sales
 */
declare(strict_types=1);

namespace Zigly\Sales\Block\Order;

use Magento\Framework\App\Request\Http;
use Magento\Framework\View\Element\Template\Context;

class Menu extends \Magento\Framework\View\Element\Template
{

    /**
     * @var $request
     */
    protected $request;

    /**
     * Constructor
     * @param Http $request
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Http $request,
        Context $context,
        array $data = []
    ) {
        $this->request = $request;
        parent::__construct($context, $data);
    }

    /*
    * Set Active menu
    */
    Public function SetActive()
    {
        $actionName = $this->request->getFullActionName();
        return $actionName;
    }
}