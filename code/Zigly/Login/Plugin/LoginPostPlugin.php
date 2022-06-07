<?php

/**
* Copyright (C) 2020  Zigly
* @package   Zigly_Login
*/

namespace Zigly\Login\Plugin;

use Magento\Customer\Model\Session;
/**
 *
 */
class LoginPostPlugin
{

    /**
     * @var Session
     */
    protected $session;


    /**
     * @param Context $context
     * @param Session $customerSession
     * @param AccountManagementInterface $customerAccountManagement
     * @param CustomerUrl $customerHelperData
     * @param Validator $formKeyValidator
     * @param AccountRedirect $accountRedirect
     */
    public function __construct(
        Session $customerSession
    ) {
        $this->session = $customerSession;
    }


    /**
     * Change redirect after login to home instead of dashboard.
     *
     * @param \Magento\Customer\Controller\Account\LoginPost $subject
     * @param \Magento\Framework\Controller\Result\Redirect $result
     */
    public function afterExecute(
        \Magento\Customer\Controller\Account\LoginPost $subject,
        $result)
    {
        // if ($this->session->isLoggedIn()) {
        //     $result->setPath('/');
        // }else{
        //     $result->setPath('customer/account/login');
        // }
        return $result;
    }

}