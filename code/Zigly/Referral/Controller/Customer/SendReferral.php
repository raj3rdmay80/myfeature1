<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Referral
 */
namespace Zigly\Referral\Controller\Customer;

use Magento\Framework\UrlInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Zigly\Referral\Helper\Data as ReferralHelper;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\App\Config\ScopeConfigInterface;

class SendReferral extends \Magento\Framework\App\Action\Action
{

    /**
    * @var ScopeConfigInterface
    */
    protected $scopeConfig;

    /**
     * @var PageFactory
    */
    protected $pageFactory;

    /**
     * @var ReferralHelper
     */
    protected $referralHelper;

    /**
    * MSG91 authkey config path
    */
    const XML_PATH_MSG_AUTHKEY = 'msggateway/general/authkey';

    /**
     * @param Context $context
     * @param UrlInterface $urlInterface
     * @param ReferralHelper $referralHelper
     * @param JsonFactory $resultJsonFactory
     * @param PageFactory $resultPageFactory
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        UrlInterface $urlInterface,
        ReferralHelper $referralHelper,
        JsonFactory $resultJsonFactory,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->pageFactory = $pageFactory;
        $this->urlInterface =$urlInterface;
        $this->referralHelper = $referralHelper;
        $this->resultJsonFactory = $resultJsonFactory;
        return parent::__construct($context);
    }

    /*
    * get auth key
    */
    public function getMsgauthkey()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_MSG_AUTHKEY, $storeScope);
    }

    public function execute()
    {
        if ($this->referralHelper->isEnabled()) {
            $resultPage = $this->pageFactory->create();
            $result = $this->resultJsonFactory->create();
            $post = $this->getRequest()->getPostValue();
            try {
                $authkey = $this->getMsgauthkey();
                $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                $smstemplateid = $this->scopeConfig->getValue('referral_program/referral_sms/send_referral_sms_template', $storeScope);
                $senderName = $this->scopeConfig ->getValue('referral_program/referral_sms/referral_sms_sender_name', $storeScope);
                $phoneNumber = $post['number'];
                $referral = $post['referral'];
                $phoneNumber = str_replace(' ', '', $phoneNumber);
                $mobileNo = explode(',', $phoneNumber);
                $max = 10;
                $numbers = array_map(function($val) use ($max) {
                    if ((strlen($val) < $max) || (strlen($val) > $max) || !(is_numeric($val))) {
                        $this->messageManager->addError(__('Please enter valid phone number.'));
                        $this->_redirect('referral/customer/index');
                        return $resultPage;
                    }
                },$mobileNo);
                if($authkey){
                    $url = $this->urlInterface->getUrl("customer/account/login",["reference" => $referral]);
                    foreach ($mobileNo as $number) {
                        $mobileNumber = '91'.$number;
                        if($authkey){
                            if(is_numeric($mobileNumber) && !empty($smstemplateid) && !empty($senderName)){
                                $curl = curl_init();
                                curl_setopt_array($curl, array(
                                  CURLOPT_URL => "https://api.msg91.com/api/v5/flow/",
                                  CURLOPT_RETURNTRANSFER => true,
                                  CURLOPT_ENCODING => "",
                                  CURLOPT_MAXREDIRS => 10,
                                  CURLOPT_TIMEOUT => 30,
                                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                  CURLOPT_CUSTOMREQUEST => "POST",
                                  CURLOPT_POSTFIELDS => "{\n  \"flow_id\": \"".$smstemplateid."\",\n  \"sender\": \"".$senderName."\",\n  \"mobiles\": \"".$mobileNumber."\",\n  \"url\": \"".$url."\"}",
                                  CURLOPT_HTTPHEADER => array(
                                    "authkey: ".$authkey."",
                                    "content-type: application/JSON"
                                  ),
                                ));
                                $response = curl_exec($curl);
                                $err = curl_error($curl);
                                curl_close($curl);
                            }
                        }
                    }
                    $this->messageManager->addSuccess(__('Invite sent successfully.'));
                } else {
                    $this->messageManager->addError(__('Can\'t send invite.'));
                }
            } catch (\Exception $e) {
                throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
            }
            $this->_redirect('referral/customer/index');
            return $resultPage;
        }
        throw new NotFoundException(__('noroute'));
    }
}