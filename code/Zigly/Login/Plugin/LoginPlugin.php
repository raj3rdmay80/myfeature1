<?php

/**
* Copyright (C) 2020  Zigly
* @package   Zigly_Login
*/

namespace Zigly\Login\Plugin;

use Magento\Customer\Model\Session;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Controller\Result\RedirectFactory;

class LoginPlugin
{

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var CookieManagerInterface
     */
    protected $cookieManager;

    /**
     * @var CookieMetadataFactory CookieMetadataFactory
     */
    protected $cookieMetadataFactory;
    /**
     * @var RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param CookieMetadataFactory $cookieMetadataFactory
     * @param RedirectFactory $resultRedirectFactory
     * @param CookieManagerInterface $cookieManager
     */
    public function __construct(
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        RedirectFactory $resultRedirectFactory,
        Session $customerSession
    ) {
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->session = $customerSession;
    }


    /**
     * Change redirect after login to home instead of dashboard.
     *
     * @param \Magento\Customer\Controller\Account\Login $subject
     * @param callable $proceed
     */
    public function aroundExecute(
        \Magento\Customer\Controller\Account\Login $subject,
        callable $proceed)
    {
        if ($this->session->isLoggedIn()) {
            $afterloginurl = $this->cookieManager->getCookie('afterloginurl');
            if (!empty($afterloginurl)) {
                $publicCookieMetadata = $this->cookieMetadataFactory->createPublicCookieMetadata();
                $publicCookieMetadata->setPath('/');
                $publicCookieMetadata->setHttpOnly(false);
                $this->cookieManager->deleteCookie('afterloginurl', $publicCookieMetadata);
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setUrl($afterloginurl);
                return $resultRedirect;
            }
        }
        $returnValue = $proceed();
        return $returnValue;
    }

}