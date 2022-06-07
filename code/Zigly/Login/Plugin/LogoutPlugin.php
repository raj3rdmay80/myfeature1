<?php

/**
* Copyright (C) 2020  Zigly
* @package   Zigly_Login
*/

namespace Zigly\Login\Plugin;

class LogoutPlugin
{
    /**
     * Change redirect after logout.
     *
     * @param \Magento\Customer\Controller\Account\Logout $subject
     * @param \Magento\Framework\Controller\Result\Redirect $result
     */
    public function afterExecute(
        \Magento\Customer\Controller\Account\Logout $subject,
        $result)
    {
        $result->setPath('/');
        return $result;
    }

}