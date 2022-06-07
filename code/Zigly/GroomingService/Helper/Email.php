<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomingService
 */
declare(strict_types=1);

namespace Zigly\GroomingService\Helper;

use Magento\Framework\App\Area;
use Magento\Framework\Escaper;
use Magento\Framework\Registry;
use Zigly\Login\Model\OtpreportFactory;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\App\Helper\Context;
use Magento\Customer\Model\SessionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Email extends AbstractHelper
{

    /**
    * @var ScopeConfigInterface
    */
    protected $scopeConfig;

    /**
    * MSG91 authkey config path
    */
    const XML_PATH_MSG_AUTHKEY = 'msggateway/general/authkey';

    /**
     * @param Curl $curl
     * @param Context $context
     * @param Escaper $escaper
     * @param Registry $registry
     * @param SerializerInterface $serializer
     * @param SessionFactory $customerSession
     * @param ScopeConfigInterface $scopeConfig
     * @param StateInterface $inlineTranslation
     * @param TransportBuilder $transportBuilder
     * @param StoreManagerInterface $storeManager
     * @param OtpreportFactory $otpreportFactory
     */
    public function __construct(
        Curl $curl,
        Context $context,
        Escaper $escaper,
        Registry $registry,
        SerializerInterface $serializer,
        SessionFactory $customerSession,
        ScopeConfigInterface $scopeConfig,
        StateInterface $inlineTranslation,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        OtpreportFactory $otpreportFactory
    ) {
        $this->curl = $curl;
        $this->escaper = $escaper;
        $this->registry = $registry;
        $this->serializer = $serializer;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->otpreportFactory = $otpreportFactory;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        parent::__construct($context);
    }

    /*
    * get auth key
    */
    public function getMsgauthkey()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_MSG_AUTHKEY, $storeScope);
    }

    /*
    * send email function
    */
    public function sendMail($templatevariable)
    {
        $this->inlineTranslation->suspend();
        try {
            $storeId = $this->storeManager->getStore()->getId();
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $email = $this->scopeConfig->getValue('trans_email/ident_sales/email', $storeScope, $storeId);
            $name = $this->scopeConfig->getValue('trans_email/ident_sales/name', $storeScope, $storeId);
            $templateid = $this->scopeConfig->getValue(''.$templatevariable['template_id'].'', $storeScope, $storeId);
            $sender = [
                'name' => $name,
                'email' => $email,
            ];
            if (!empty($templateid) && !empty($templatevariable['email'])){
                $transport = $this->transportBuilder
                    ->setTemplateIdentifier($templateid)
                    ->setTemplateOptions(
                        [
                            'area' => Area::AREA_FRONTEND,
                            'store' => $storeId,
                        ]
                    )
                    ->setTemplateVars(['name' => $templatevariable['name']])
                    ->setFrom($sender)
                    ->addTo($templatevariable['email'])
                    ->getTransport();
                $transport->sendMessage();
            }
        } catch (\Exception $e) {
            //$this->logger->debug($e->getMessage());
        }
        $this->inlineTranslation->resume();
        return $this;
    }

    public function sendBookingSms($bookingVar)
    {
        $authkey = $this->getMsgauthkey();
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $smstemplateid = $this->scopeConfig->getValue(''.$bookingVar['template_id'].'', $storeScope);
        $senderName = $this->scopeConfig ->getValue('msggateway/servicesbookingemail/success_booking_sms_sender_name', $storeScope);
        $optputmsg = 'sms sent Successfully';
        $mobileNo = str_replace(' ', '', $bookingVar['mobile_no']);
        $name = $bookingVar['name'];
        $mobileNo = '91'.$mobileNo;
        if($authkey){
            if(is_numeric($mobileNo) && $smstemplateid){
                $curl = curl_init();
                curl_setopt_array($curl, array(
                  CURLOPT_URL => "https://api.msg91.com/api/v5/flow/",
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => "",
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 30,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => "POST",
                  CURLOPT_POSTFIELDS => "{\n  \"flow_id\": \"".$smstemplateid."\",\n  \"sender\": \"".$senderName."\",\n  \"mobiles\": \"".$mobileNo."\",\n  \"name\": \"".$name."\"}",
                  CURLOPT_HTTPHEADER => array(
                    "authkey: ".$authkey."",
                    "content-type: application/JSON"
                  ),
                ));
                $response = curl_exec($curl);
                $err = curl_error($curl);
                curl_close($curl);
                if ($err) {
                    $optputmsg = "cURL Error #:" . $err;
                }
            }
        }else{
            $optputmsg = "Please check with admin.Sms gateway is not integrate";
        }
        $outputdata = ['msg' => $optputmsg];
        return $outputdata;
    }

    public function sendServiceTeamSms($serviceVar)
    {
        $authkey = $this->getMsgauthkey();
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $smstemplateid = $this->scopeConfig->getValue(''.$serviceVar['template_id'].'', $storeScope);
        $senderName = $this->scopeConfig ->getValue('msggateway/servicesbookingemail/service_team_sender_name', $storeScope);
        $mobileNo = $serviceVar['mobile_no'];
        foreach ($mobileNo as $number) {
            $mobileNo = '91'.$number;
            if($authkey){
                if(is_numeric($mobileNo) && !empty($smstemplateid) && !empty($senderName)){
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                      CURLOPT_URL => "https://api.msg91.com/api/v5/flow/",
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_ENCODING => "",
                      CURLOPT_MAXREDIRS => 10,
                      CURLOPT_TIMEOUT => 30,
                      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                      CURLOPT_CUSTOMREQUEST => "POST",
                      CURLOPT_POSTFIELDS => "{\n  \"flow_id\": \"".$smstemplateid."\",\n  \"sender\": \"".$senderName."\",\n  \"mobiles\": \"".$mobileNo."\"}",
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
        return true;
    }
}
