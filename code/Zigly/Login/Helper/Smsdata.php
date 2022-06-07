<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Login
 */
namespace Zigly\Login\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Escaper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Serialize\SerializerInterface;

class Smsdata extends AbstractHelper
{
    /**
    * @var \Magento\Framework\App\Config\ScopeConfigInterface
    */
    protected $scopeConfig;

    /**
    * MSG91 authkey config path
    */
    const XML_PATH_MSG_AUTHKEY = 'msggateway/general/authkey';

    /**
    * MSG91 static otp email config path
    */
    const XML_PATH_MSG_STATIC_EMAIL= 'msggateway/otpemail/static_otp_email';

    /**
    * MSG91 static otp phone config path
    */
    const XML_PATH_MSG_STATIC_PHONE = 'msggateway/otpemail/static_otp_phone';

    public function __construct(
        Context $context,
        \Magento\Customer\Model\SessionFactory $customerSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\HTTP\Client\Curl $curl,
        StateInterface $inlineTranslation,
        Escaper $escaper,
        TransportBuilder $transportBuilder,
        \Zigly\Login\Model\OtpreportFactory $otpreportFactory,
        SerializerInterface $serializer,
        StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry
    ) {
        $this->_customerSession = $customerSession;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->curl = $curl;
        $this->inlineTranslation = $inlineTranslation;
        $this->escaper = $escaper;
        $this->transportBuilder = $transportBuilder;
        $this->_registry = $registry;
        $this->otpreportFactory = $otpreportFactory;
        $this->serializer = $serializer;
        parent::__construct($context);

    }
    public function getMsgauthkey() {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_MSG_AUTHKEY, $storeScope);
    }

    public function getStaticOtpEmail() {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_MSG_STATIC_EMAIL, $storeScope);
    }

    public function getStaticOtpPhone() {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_MSG_STATIC_PHONE, $storeScope);
    }

     /*send email function*/
    public function sendmail($templatevariable)
    {
        try {
            $this->inlineTranslation->suspend();
            $storeId = $this->storeManager->getStore()->getId();
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $email = $this->scopeConfig->getValue('trans_email/ident_support/email', $storeScope, $storeId);
            $name = $this->scopeConfig->getValue('trans_email/ident_support/name', $storeScope, $storeId);
            $templateid = $this->scopeConfig->getValue('msggateway/otpemail/login_email_otp', $storeScope, $storeId);
            $sender = [
                'name' => $name,
                'email' => $email,
            ];
            $transport = $this->transportBuilder
                ->setTemplateIdentifier($templateid)
                ->setTemplateOptions(
                    [
                        'area' => 'frontend',
                        'store' => $storeId,
                    ]
                )
                ->setTemplateVars(['name' => $templatevariable['name'],'otp' => $templatevariable['otp']])
                ->setFrom($sender)
                ->addTo($templatevariable['email'])
                ->getTransport();

            $transport->sendMessage();
            $this->inlineTranslation->resume();
            return true;
        } catch (\Exception $e) {
          print_r($e->getMessage());
            //$this->logger->debug($e->getMessage());
            return false;
        }
    }
    public function verifyotp($mobile_no,$otp){
        $authkey = $this->getMsgauthkey();
        $error = '';
        $status = 0;
        $mobile_no = '91'.$mobile_no;
        if(is_numeric($mobile_no)){
            $curlUrl = "https://api.msg91.com/api/v5/otp/verify?mobile=$mobile_no&otp=$otp&authkey=$authkey";
            $curl = $this->curl;
            $curl->setOptions(array(
              CURLOPT_URL => $curlUrl,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS => "",
              CURLOPT_SSL_VERIFYHOST => 0,
              CURLOPT_SSL_VERIFYPEER => 0,
            ));

            $err = $curl->post($curlUrl, []);
            $response = $curl->getBody();
            if ($err) {
               $optputmsg = "cURL Error #:" . $err;
              //echo "cURL Error #:" . $err;
            } else {
                $http_status = $curl->getStatus();
                $resultArray = json_decode($response, true);
                //echo "<pre>";print_r($resultArray);
                if(isset($resultArray['type']) && $resultArray['type'] != 'success'){
                    $optputmsg = $resultArray['type'];
                    if(isset($resultArray['message'])){
                        $error = $resultArray['type'] . " : ".$resultArray['message'];
                        $optputmsg = $resultArray['message'];
                    }
                 }else{
                    $optputmsg = "OTP verfied Successfully";
                    $status = 1;
                 }
            }
        }else{
            $optputmsg = "Mobile is not Numeric";
        }
        $outputdata = ['msg' => $optputmsg,'otp' => $otp, 'status' => $status,'error' => $error];
        return $outputdata;
    }

    public function resendloginotp($mobile_no){
        $authkey = $this->getMsgauthkey();
        $status = 0;
        $mobile_no = '91'.$mobile_no;
        if(is_numeric($mobile_no)){
                $curlsend = $this->curl;
                $curlsendurl = 'https://api.msg91.com/api/v5/otp/retry?mobile='.$mobile_no.'&authkey='.$authkey.'&retrytype=text';
                $curlsend->setOptions(array(
                  CURLOPT_URL => $curlsendurl,
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => "",
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 30,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => "POST",
                  CURLOPT_POSTFIELDS => "",
                  CURLOPT_SSL_VERIFYHOST => 0,
                  CURLOPT_SSL_VERIFYPEER => 0,
                ));

                $errsend = $curlsend->post($curlsendurl, []);
                $responsesend= $curlsend->getBody();
                  if ($errsend) {
                    $optputmsg = "cURL Error #:" . $err;
                  } else {
                     $http_status = $curlsend->getStatus();
                     $resultArray = json_decode($responsesend, true);
                     //echo "<pre>";print_r($resultArray);exit();
                     if(isset($resultArray['type']) && $resultArray['type'] != 'success'){
                        $optputmsg = $resultArray['type'] . " : OTP not send";
                        if(isset($resultArray['message'])){
                            //$optputmsg = $resultArray['type'] . " : ".$resultArray['message'];
                            $optputmsg = $resultArray['message'];
                        }
                     }else{
                        $optputmsg = "Resend OTP sent successfully";
                        $status = 1;
                     }
                }
        }else{
            $optputmsg = "Mobile is not Numeric";
        }
        $outputdata = ['msg' => $optputmsg, 'status' => $status];
        return $outputdata;
    }
    public function sendloginotp($mobile_no, $templateId = 'msggateway/otpemail/login_mobile_otp'){
        $status = 0;
        $otpVal = 0;
        $authkey = $this->getMsgauthkey();
        $name = '';
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $smstemplateid = $this->scopeConfig->getValue(''.$templateId.'', $storeScope);
        $optputmsg = 'Something Went wrong';
        $mobile_no = str_replace(' ', '', $mobile_no);
        if($authkey){
            if(is_numeric($mobile_no)){
                $checkbalance = $this->msgcheckbalance();
                if($checkbalance == 1){
                    $otp = rand(1000,9999);
                    $staticPhoneNo = explode(",", $this->getStaticOtpPhone());
                    if (count($staticPhoneNo) && in_array($mobile_no, $staticPhoneNo)) {
                        $otp = 1200;
                    }
                    $mobile_no = '91'.$mobile_no;
                    /* $curlsendurl = 'https://api.msg91.com/api/v5/otp?authkey='.$authkey.'&template_id='.$smstemplateid.'&otp_expiry=10&mobile='.$mobile_no.'&otp='.$otp.'&extra_param={"NAME":"'.$name.'"}';*/
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => 'https://api.msg91.com/api/v5/otp?template_id='.$smstemplateid.'&otp_expiry=5&mobile='.$mobile_no.'&authkey='.$authkey.'',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "GET",
                        CURLOPT_POSTFIELDS => "{\"otp\":\"".$otp."\"}",
                        CURLOPT_HTTPHEADER => array(
                            "content-type: application/json"
                        ),
                    ));

                    $responsesend = curl_exec($curl);
                    $errsend = curl_error($curl);

                    curl_close($curl);
                    if ($errsend) {
                        $optputmsg = "cURL Error #:" . $errsend;
                    } else {
                        /*$http_status = $curl->getStatus();*/
                        $resultArray = json_decode($responsesend, true);
                        if(isset($resultArray['type']) && $resultArray['type'] != 'success'){
                            $optputmsg = $resultArray['type'] . " : OTP not send";
                        }else{
                            if(isset($resultArray['type']) && $resultArray['type'] == 'success'){
                                $dataser = ['otp' => $otp,'ipaddress' => $_SERVER['REMOTE_ADDR']];
                                $additionalData = $this->serializer->serialize($dataser);
                                /*$this->otpreportFactory->create()->setRequestId($resultArray['request_id'])->setUsername($mobile_no)->setOtpvalue($additionalData)->save();*/
                                $otpreport = $this->otpreportFactory->create()->getCollection()->addFieldToFilter('username',$mobile_no)->getFirstItem();
                                if($otpreport->getEntityId()){
                                     $record = $this->otpreportFactory->create()->load($otpreport->getEntityId());
                                     $record->setRequestId($resultArray['request_id'])->setUsername($mobile_no)->setData('otpfailedtime', 0)->setOtpvalue($additionalData)->setFlag(0)->save();
                                }else{
                                    $this->otpreportFactory->create()->setRequestId($resultArray['request_id'])->setUsername($mobile_no)->setData('otpfailedtime', 0)->setOtpvalue($additionalData)->save();
                                }
                                $optputmsg = "OTP sent Successfully";
                                $otpVal = $otp;
                                $status = 1;
                            }
                        }
                    }
                }else{
                    $optputmsg = $checkbalance;
                }
            }else{
                $optputmsg = "Please check with mobile number";
            }
        }else{
            $optputmsg = "Please check with admin.Sms gateway is not integrate";
        }
        $outputdata = ['msg' => $optputmsg,'otp' => $otpVal, 'status' => $status];
        return $outputdata;
    }

    public function msgcheckbalance(){
        $authkey = $this->getMsgauthkey();
        $checkbalancemsg = 1;
        $curlUrl = "https://api.msg91.com/api/balance.php?authkey=$authkey&type=106";
        $curl = $this->curl;
        $curl->setOptions(array(
          CURLOPT_URL => $curlUrl,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_SSL_VERIFYHOST => 0,
          CURLOPT_SSL_VERIFYPEER => 0,
        ));

       $err = $curl->post($curlUrl, []);
       $response = $curl->getBody();
        if ($err) {
            $checkbalancemsg = "cURL Error #:" . $err;
        } else {
            $http_status = $curl->getStatus();
            $resultArray = json_decode($response, true);
            if(isset($resultArray['msg'])){
                $checkbalancemsg = $resultArray['msg']." : ".$resultArray['msgType'];
            }else{
                if((int)$response < 0 ){
                    $checkbalancemsg = "Not enough OTP credits in your account";
                }else{
                    $checkbalancemsg = 1;
                }
            }
        }
        return $checkbalancemsg;
    }
}
