<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Managepets
 */
namespace Zigly\Managepets\Controller\Index;

use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Magento\Customer\Model\Registration;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;

class Form extends \Magento\Customer\Controller\AbstractAccount implements HttpGetActionInterface
{
    /**
     * @var \Magento\Customer\Model\Registration
     */
    protected $registration;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param PageFactory $resultPageFactory
     * @param Registration $registration
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory,
        \Zigly\Managepets\Model\ManagepetsFactory $managepetsFactory,
        Registration $registration
    ) {
        $this->session = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->registration = $registration;
        $this->managepetsFactory = $managepetsFactory;
        parent::__construct($context);
    }

    /**
     * Customer register form page
     *
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
       if ($this->session->isLoggedIn()) {
            $id = $this->getRequest()->getParam('id');
            if((int)$id){
                $customerid = $this->session->getCustomer()->getId();
                $petdetail = $this->managepetsFactory->create()->load((int)$id);
                if(!$petdetail->getCustomerId() || $petdetail->getCustomerId() !== $customerid){
                    $this->messageManager->addError(__('This data no longer exist.'));
                    $resultRedirect = $this->resultRedirectFactory->create();
                    $resultRedirect->setPath('managepets');
                    return $resultRedirect;
                }else{
                    $resultPage = $this->resultPageFactory->create();
                    $resultPage->getConfig()->getTitle()->set(__(""));
                    $navigationBlock = $resultPage->getLayout()->getBlock('customer_account_navigation');
                    if ($navigationBlock) {
                        $navigationBlock->setActive('managepets');
                    }
                    return $resultPage;
                }
            }else{
                $resultPage = $this->resultPageFactory->create();
                $resultPage->getConfig()->getTitle()->set(__(""));
                $navigationBlock = $resultPage->getLayout()->getBlock('customer_account_navigation');
                if ($navigationBlock) {
                    $navigationBlock->setActive('managepets');
                }
                return $resultPage;
            }
       }else{
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*/*');
            return $resultRedirect;
       }
    }
}
