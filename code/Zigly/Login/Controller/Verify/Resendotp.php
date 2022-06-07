<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Login
 */
namespace Zigly\Login\Controller\Verify;

use Zigly\Login\Helper\Smsdata;
use Magento\Customer\Model\Session;
use Zigly\Login\Model\OtpreportFactory;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;


class Resendotp extends Action
{

    public function __construct(
        Curl $curl,
        Context $context,
        Smsdata $helperdata,
        JsonFactory $resultJsonFactory,
        PageFactory $resultPageFactory,
        SerializerInterface $serializer,
        CustomerFactory $customerFactory,
        ScopeConfigInterface $scopeConfig,
        OtpreportFactory $otpreportFactory,
        StoreManagerInterface $storeManager,
        CustomerCollectionFactory $customerCollectionFactory,
        AccountManagementInterface $customerAccountManagement
    ) {
        $this->curl = $curl;
        $this->helper = $helperdata;
        $this->serializer = $serializer;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->customerFactory = $customerFactory;
        $this->otpreportFactory = $otpreportFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->customerCollectionFactory = $customerCollectionFactory;
        parent::__construct($context);
    }


    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $username = $data['username'];
        $oldEmail = $data['oldemail'];
        $oldphonenumber = $data['oldphonenumber'];
        $templatevariable['otp'] = 0;
        $templatevariable['status'] = 0;
        $templatevariable['msg'] = 'something went wrong';
        $websiteId = (int)$this->storeManager->getWebsite()->getId();
        if(!is_numeric($username) && $oldEmail != $username){
            $username = str_replace(" ", "", $username);
            $otpreport = $this->otpreportFactory->create()->getCollection()->addFieldToFilter('username',$username)->getFirstItem();
            if($otpreport->getEntityId()){
                $otpdata = $otpreport->getOtpvalue();
                if($otpdata){
                    $additionalData = $this->serializer->unserialize($otpdata);
                    $atttime =(int)$additionalData['atttime'];
                    $otpfailedtime = 0;
                    if($atttime >= 3){
                        $templatevariable['msg'] = 'You have exceeded the limit of resending the OTP! Please try again after 5 mins';
                        $otpfailedtime = 4;
                    }else{
                        $expirycheck = time() - $additionalData['time'];
                        $expirymin = round(abs($expirycheck) / 60,2);
                        if($expirymin > (int)$additionalData['expiry']){
                            $templatevariable['msg'] = 'OTP expired';
                        }else{
                            $otp =(int)$additionalData['otp'];
                            $templatevariable['name'] = '';
                            $templatevariable['email'] = $username;
                            $templatevariable['otp'] = $additionalData['otp'];
                            $atttime = (int)$additionalData['atttime'] + 1;
                            $dataser = ['otp' => $additionalData['otp'],'time' => time(),'expiry' => 5,'atttime' => $atttime];
                            $additionalData = $this->serializer->serialize($dataser);
                            $record = $this->otpreportFactory->create()->load($otpreport->getEntityId());
                            $record->setUsername($username)->setOtpvalue($additionalData)->setData('otpfailedtime', $otpfailedtime)->save();
                            $this->helper->sendmail($templatevariable);
                            $templatevariable['status'] = 1;
                            $templatevariable['msg'] ='Resend OTP send Successfully';
                        }
                    }
                }else{
                    $templatevariable['msg'] = 'OTP not found';
                }
            }else{
                $templatevariable['msg'] = 'OTP not found';
            }
        }else{
            if (is_numeric($username) && $username != $oldphonenumber && strlen($username) == 10) {
                $templatevariable = $this->helper->resendloginotp($username);
            }
        }
        $result = $this->resultJsonFactory->create();
        $resultPage = $this->resultPageFactory->create();
        unset($templatevariable['otp']);
        $result->setData($templatevariable);
        return $result;
    }
}