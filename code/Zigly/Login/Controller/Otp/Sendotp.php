<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Login
 */
namespace Zigly\Login\Controller\Otp;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;


class Sendotp extends Action
{

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        PageFactory $resultPageFactory,
        AccountManagementInterface $customerAccountManagement,
        StoreManagerInterface $storeManager,
        CustomerCollectionFactory $customerCollectionFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        SerializerInterface $serializer,
        \Zigly\Login\Helper\Smsdata $helperdata,
        \Zigly\Login\Model\OtpreportFactory $otpreportFactory
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->storeManager = $storeManager;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->scopeConfig = $scopeConfig;
        $this->curl = $curl;
        $this->customerFactory = $customerFactory;
        $this->serializer = $serializer;
        $this->helper = $helperdata;
        $this->otpreportFactory = $otpreportFactory;
        parent::__construct($context);
    }


    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $username = $data['username'];
        $templatevariable['otp'] = 0;
        $templatevariable['status'] = 0;
        $templatevariable['checkaccount'] = 0;
        $websiteId = (int)$this->storeManager->getWebsite()->getId();
        if(!is_numeric($username)){
            $otp = rand(1000,9999);
            $staticEmails = explode(",", $this->helper->getStaticOtpEmail());
            if (count($staticEmails) && in_array($username, $staticEmails)) {
                $otp = 1200;
            }
            //$otp = 4321;
            $dataser = ['otp' => $otp,'time' => time(),'expiry' => 5,'atttime' => 0];
            $additionalData = $this->serializer->serialize($dataser);
            $username = str_replace(" ", "", $username);
            $otpreport = $this->otpreportFactory->create()->getCollection()->addFieldToFilter('username',$username)->getFirstItem();
            if($otpreport->getEntityId()){
                 $record = $this->otpreportFactory->create()->load($otpreport->getEntityId());
                 $record->setUsername($username)->setData('otpfailedtime', 0)->setOtpvalue($additionalData)->save();
            }else{
                $this->otpreportFactory->create()->setUsername($username)->setData('otpfailedtime', 0)->setOtpvalue($additionalData)->save();
            }
            $templatevariable['name'] = '';
            $templatevariable['email'] = $username;
            $templatevariable['otp'] = $otp;
            $this->helper->sendmail($templatevariable);
            $templatevariable['status'] = 1;
        }else{
            $templatevariable = $this->helper->sendloginotp($username);
        }
        $result = $this->_resultJsonFactory->create();
        $resultPage = $this->_resultPageFactory->create();
        unset($templatevariable['otp']);
        $result->setData($templatevariable);
        return $result;
    }
}
