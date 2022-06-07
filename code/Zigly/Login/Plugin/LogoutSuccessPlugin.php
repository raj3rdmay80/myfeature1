<?php

/**
* Copyright (C) 2020  Zigly
* @package   Zigly_Login
*/

namespace Zigly\Login\Plugin;

use Magento\Framework\Controller\ResultFactory;

class LogoutSuccessPlugin
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param ResultFactory $resultFactory
     */
    public function __construct(
        ResultFactory $resultFactory
    ) {
        $this->resultFactory = $resultFactory;
    }

    /**
     * Change redirect after logout success.
     *
     * @param \Magento\Customer\Controller\Account\Logout $subject
     * @return \Magento\Framework\View\Result\Page
     * 
     */
    public function aroundExecute (
        \Magento\Customer\Controller\Account\LogoutSuccess $subject,
        callable $proceed
    ) {
        $redirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
        $redirect->setUrl('/');
        return $redirect;
    }
}