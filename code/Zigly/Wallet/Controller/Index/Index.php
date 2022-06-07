<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Wallet
 */
declare(strict_types=1);

namespace Zigly\Wallet\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Exception\NotFoundException;
use Magento\Customer\Model\Session as CustomerSession;
use Zigly\Wallet\Helper\Data as WalletHelper;

class Index extends \Magento\Framework\App\Action\Action
{

    /**
     * Customer session model
     *
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var PageFactory
     */
    protected $pageFactory;

    /**
     * @var WalletHelper
     */
    protected $walletHelper;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param CustomerSession $customerSession
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        WalletHelper $walletHelper,
        CustomerSession $customerSession
    ) {
        $this->pageFactory = $pageFactory;
        $this->walletHelper = $walletHelper;
        $this->customerSession = $customerSession;
        return parent::__construct($context);
    }

    public function execute()
    {
        if ($this->walletHelper->isEnabled() && $this->customerSession->isLoggedIn()) {
            $resultPage = $this->pageFactory->create();
            /** @var \Magento\Theme\Block\Html\Breadcrumbs */
            $breadcrumbs = $resultPage->getLayout()->getBlock('breadcrumbs');
            $breadcrumbs->addCrumb('home',
                [
                    'label' => __('Home'),
                    'title' => __('Home'),
                    'link' => $this->_url->getUrl('')
                ]
            );
            $breadcrumbs->addCrumb('wallet',
                [
                    'label' => __('Wallet'),
                    'title' => __('Wallet')
                ]
            );
            return $resultPage;
        }
        return $this->_redirect('/');
    }
}