<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_VetConsulting
 */
declare(strict_types=1);

namespace Zigly\VetConsulting\Controller\Insta;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\UrlInterface;

class Consult extends \Magento\Framework\App\Action\Action
{

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Customer session model
     *
     * @var Session
     */
    protected $customerSession;

    /**
     * @var CookieManagerInterface
     */
    protected $cookieManager;

    /**
     * @var CookieMetadataFactory
     */
    protected $cookieMetadataFactory;

    /**
     * @var UrlInterface
     */
    protected $urlInterface;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Session $customerSession
     * @param CookieManagerInterface $cookieManager
     * @param CookieMetadataFactory $cookieMetadataFactory
     * @param UrlInterface $urlInterface
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        UrlInterface $urlInterface,
        PageFactory $resultPageFactory
    ) {
        $this->customerSession = $customerSession;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->urlInterface = $urlInterface;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if (!$this->customerSession->isLoggedIn()) {
            $publicCookieMetadata = $this->cookieMetadataFactory->createPublicCookieMetadata();
            $publicCookieMetadata->setDuration('900');
            $publicCookieMetadata->setPath('/');
            $publicCookieMetadata->setHttpOnly(false);
            $this->cookieManager->setPublicCookie('afterloginurl', $this->urlInterface->getCurrentUrl(), $publicCookieMetadata);
            return $this->_redirect('customer/account/login');
        }
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set("Vet Consulting");
        /** @var \Magento\Theme\Block\Html\Breadcrumbs */
        $breadcrumbs = $resultPage->getLayout()->getBlock('breadcrumbs');
        $breadcrumbs->addCrumb('home',
            [
                'label' => __('Home'),
                'title' => __('Home'),
                'link' => $this->_url->getUrl('')
            ]
        );
        $breadcrumbs->addCrumb('vetconsulting',
            [
                'label' => __('Vet Consulting'),
                'title' => __('Vet Consulting')
            ]
        );
        $breadcrumbs->addCrumb('insta',
            [
                'label' => __('Insta Consult'),
                'title' => __('Insta Consult')
            ]
        );
        return $resultPage;
    }
}

