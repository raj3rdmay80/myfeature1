<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Login
 */
namespace Zigly\Login\Controller\Verify;

use Zigly\Login\Helper\Smsdata;
use Magento\Customer\Model\Session;
use Magento\Framework\HTTP\Client\Curl;
use Zigly\Login\Model\OtpreportFactory;
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


class Verifyotp extends Action
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
        $result = $this->resultJsonFactory->create();
        $data = $this->getRequest()->getPostValue();
        $username = $data['username'];
        $status = 1;
        $errormsg = 'Verified successfully';
        $otp = $data['otp'];
        if (!isset($otp) || empty($otp) || !is_numeric($otp) || strlen($otp) > 4 || strlen($otp) < 4) {
            $status = 0;
            $errormsg = 'Please enter valid OTP';
            $result->setData(['status' => $status,'msg' => $errormsg]);
            return $result;
        }
        if(is_numeric($username) &&  strlen($username) == 10) {
            $verifydata = $this->helper->verifyotp($username,$otp);
            if($verifydata['status'] != 1){
                $errormsg = $verifydata['msg'];
                if ($errormsg == "OTP not match") {
                    $errormsg = "Invalid";
                }
                $status = 0;
                $errormsg = 'Please enter valid OTP';
                $result->setData(['status' => $status,'msg' => $errormsg]);
                return $result;
            }
        } else {
            $username = str_replace(" ", "", $username);
            $otpreport = $this->otpreportFactory->create()->getCollection()->addFieldToFilter('username',$username)->getFirstItem();
            $errormsg = '';
            if($otpreport->getEntityId()){
                $otpdata = $otpreport->getOtpvalue();
                if($otpdata){
                    $additionalData = $this->serializer->unserialize($otpdata);
                    $expirycheck = time() - $additionalData['time'];
                    $expirymin =round(abs($expirycheck) / 60,2);
                    if($expirymin > (int)$additionalData['expiry']){
                        $status = 0;
                        $errormsg = 'OTP expired';
                    }else{
                        $errormsg = 'Verified successfully';
                        if((int)$additionalData['otp'] != $otp){
                            $status = 0;
                            $errormsg = 'Invalid';
                        }
                    }
                }else{
                    $status = 0;
                    $errormsg = 'OTP not found';
                }
            }
        }
        $resultPage = $this->resultPageFactory->create();
        $result->setData(['status' => $status,'msg' => $errormsg]);
        return $result;
    }
}