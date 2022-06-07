<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Sales
 */
declare(strict_types=1);

namespace Zigly\Sales\Helper;

use Magento\Framework\Escaper;
use Magento\Framework\App\Area;
use Magento\Framework\Registry;
use Zigly\Login\Model\OtpreportFactory;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\App\Helper\Context;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Serialize\SerializerInterface;
// use Magento\Framework\Mail\Template\TransportBuilder;
use Zigly\GroomingService\Model\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Data extends AbstractHelper
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
     * @param StoreManagerInterface $storeManager
     * @param TransportBuilder $transportBuilder
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

            $transport = $this->transportBuilder->setTemplateIdentifier(
                    $templateid
                )->setTemplateOptions(
                    [
                        'area' => Area::AREA_FRONTEND,
                        'store' => $storeId
                    ]
                )->setTemplateVars(
                    ['cancelDetails' => $templatevariable['cancelDetails']]
                )->setFrom(
                    $sender
                )->addTo(
                    $templatevariable['email']
                )->getTransport();
            $transport->sendMessage();
        } catch (\Exception $e) {
            //$this->logger->debug($e->getMessage());
        }
        $this->inlineTranslation->resume();
        return $this;
    }


    /*
    * send email function
    */
    public function sendMailWithPdf($templatevariable, $fileContent, $fileName)
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
            $this->transportBuilder->addAttachment(
            $fileContent,
            'application/pdf',
            \Zend_Mime::DISPOSITION_ATTACHMENT,
            \Zend_Mime::ENCODING_BASE64,
            $fileName
        );   

            $transport = $this->transportBuilder->setTemplateIdentifier(
                    $templateid
                )->setTemplateOptions(
                    [
                        'area' => Area::AREA_FRONTEND,
                        'store' => $storeId
                    ]
                )->setTemplateVars(
                    ['cancelDetails' => $templatevariable['cancelDetails']]
                // )->addAttachment(
                //     file_get_contents($filePath),
                //     $fileName,
                //     'application/pdf'
                )->setFrom(
                    $sender
                )->addTo(
                    $templatevariable['email']
                )->getTransport();
            $transport->sendMessage();
        } catch (\Exception $e) {
            $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/Exception.log');
            $logger = new \Zend_Log();
            $logger->addWriter($writer);
            $logger->info($e->getMessage());
            // $this->logger->debug($e->getMessage());
        }
        $this->inlineTranslation->resume();
        return $this;
    }

    public function sendCancelSms($cancelVar)
    {
        $authkey = $this->getMsgauthkey();
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $smstemplateid = $this->scopeConfig->getValue(''.$cancelVar['templateid'].'', $storeScope);
        $senderName = $this->scopeConfig ->getValue('msggateway/servicesbookingemail/cancel_booking_sms_sender_name', $storeScope);
        $optputmsg = 'Sms sent Successfully';
        $mobileNo = trim($cancelVar['mobileNo']);
        $service = $cancelVar['service'];
        $name = $cancelVar['name'];
        $hours = $cancelVar['hours'];
        $date = $cancelVar['date'];
        $path = $cancelVar['url'];
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
                  CURLOPT_POSTFIELDS => "{\n  \"flow_id\": \"".$smstemplateid."\",\n  \"sender\": \"".$senderName."\",\n  \"mobiles\": \"".$mobileNo."\",\n  \"service\": \"".$service."\",\n  \"name\": \"".$name."\",\n  \"hours\": \"".$hours."\",\n  \"date\": \"".$date."\"\n,\n  \"url\": \"".$path."\"\n}",
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

    /*
    * send order cancel reason sms
    */
    public function sendCancelReasonSms($cancelVar)
    {
        $authkey = $this->getMsgauthkey();
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $smstemplateid = $this->scopeConfig->getValue(''.$cancelVar['templateid'].'', $storeScope);
        $senderName = $this->scopeConfig ->getValue('order/order_cancel_reason_config/cancel_reason_sms_sender_name', $storeScope);
        $mobileNo = trim($cancelVar['mobileNo']);
        $orderId = $cancelVar['order_id'];
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
                  CURLOPT_POSTFIELDS => "{\n  \"flow_id\": \"".$smstemplateid."\",\n  \"sender\": \"".$senderName."\",\n  \"mobiles\": \"".$mobileNo."\",\n  \"orderid\": \"".$orderId."\"\n}",
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
        return true;
    }

    /*
    * send order return sms
    */
    public function sendReturnReasonSms($returnVar)
    {
        $authkey = $this->getMsgauthkey();
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $smstemplateid = $this->scopeConfig->getValue(''.$returnVar['templateid'].'', $storeScope);
        $senderName = $this->scopeConfig ->getValue('order/order_cancel_reason_config/return_reason_sms_sender_name', $storeScope);
        $mobileNo = trim($returnVar['mobileNo']);
        $orderId = $returnVar['order_id'];
        $days = $returnVar['days'];
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
                  CURLOPT_POSTFIELDS => "{\n  \"flow_id\": \"".$smstemplateid."\",\n  \"sender\": \"".$senderName."\",\n  \"mobiles\": \"".$mobileNo."\",\n  \"orderid\": \"".$orderId."\",\n  \"days\": \"".$days."\"\n}",
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
        return true;
    }

    /*
    * send order cancel return wallet sms
    */
    public function sendOrderCancelReturnWalletSms($walletVar)
    {
        $authkey = $this->getMsgauthkey();
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $smstemplateid = $this->scopeConfig->getValue(''.$walletVar['templateid'].'', $storeScope);
        $senderName = $this->scopeConfig ->getValue('order/order_cancel_reason_config/cancel_reason_sms_sender_name', $storeScope);
        $mobileNo = trim($walletVar['mobileNo']);
        $orderId = $walletVar['order_id'];
        $amount = $walletVar['amount'];
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
                  CURLOPT_POSTFIELDS => "{\n  \"flow_id\": \"".$smstemplateid."\",\n  \"sender\": \"".$senderName."\",\n  \"mobiles\": \"".$mobileNo."\",\n  \"amount\": \"".$amount."\",\n  \"orderid\": \"".$orderId."\"\n}",
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
        return true;
    }
}
