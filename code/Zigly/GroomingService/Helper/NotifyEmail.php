<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomingService
 */
declare(strict_types=1);

namespace Zigly\GroomingService\Helper;

use Psr\Log\LoggerInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Area;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;


class NotifyEmail extends AbstractHelper
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Notify groome email template path
     */
    const NOTIFY_GROOMER_EMAIL_TEMPLATE = 'groomingservice/emailtemplate/addgroomer_notify_groomer';

    /**
     * Notify customer email template path
     */
    const NOTIFY_CUSTOMER_EMAIL_TEMPLATE = 'groomingservice/emailtemplate/addgroomer_notify_customer';

    /**
     * Cancel notify groome email template path
     */
    const CANCEL_NOTIFY_GROOMER_EMAIL_TEMPLATE = 'groomingservice/emailtemplate/cancel_notify_groomer';

    /**
     * Cancel notify customer email template path
     */
    const CANCEL_NOTIFY_CUSTOMER_EMAIL_TEMPLATE = 'groomingservice/emailtemplate/cancel_notify_customer';

    /**
     * Rescheduled notify groome email template path
     */
    const RESCHEDULED_NOTIFY_GROOMER_EMAIL_TEMPLATE = 'groomingservice/emailtemplate/rescheduled_notify_groomer';

    /**
     * Cancel notify customer email template path
     */
    const RESCHEDULED_NOTIFY_CUSTOMER_EMAIL_TEMPLATE = 'groomingservice/emailtemplate/rescheduled_notify_customer';

    /**
    * MSG91 authkey config path
    */
    const XML_PATH_MSG_AUTHKEY = 'msggateway/general/authkey';

    /**
     * @var StateInterface
     */
    private $inlineTranslation;

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * Data constructor.
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param TransportBuilder $transportBuilder
     * @param StateInterface $inlineTranslation
     * @param ScopeConfigInterface $scopeConfig
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation,
        ScopeConfigInterface $scopeConfig,
        LoggerInterface $logger
    ) {
        $this->storeManager = $storeManager;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * Send Mail
     * @param array $templatevariable
     * @return $this
     *
     * @throws LocalizedException
     * @throws MailException
     */
    public function sendMail($templatevariable)
    {
        $this->inlineTranslation->suspend();
        try{
            $storeId = $this->getStoreId();
            $email = $this->scopeConfig->getValue('trans_email/ident_support/email', ScopeInterface::SCOPE_STORE, $storeId);
            $name = $this->scopeConfig->getValue('trans_email/ident_support/name', ScopeInterface::SCOPE_STORE, $storeId);
            
            switch ($templatevariable['email_type']) {
                case "addgroomer_notify_groomer":
                    $templateId = self::NOTIFY_GROOMER_EMAIL_TEMPLATE;
                    break;
                case "addgroomer_notify_customer":
                    $templateId = self::NOTIFY_CUSTOMER_EMAIL_TEMPLATE;
                    break;
                case "cancel_notify_groomer":
                    $templateId = self::CANCEL_NOTIFY_GROOMER_EMAIL_TEMPLATE;
                    break;
                case "cancel_notify_customer":
                    $templateId = self::CANCEL_NOTIFY_CUSTOMER_EMAIL_TEMPLATE;
                    break;
                case "rescheduled_notify_groomer":
                    $templateId = self::RESCHEDULED_NOTIFY_GROOMER_EMAIL_TEMPLATE;
                    break;
                case "rescheduled_notify_customer":
                    $templateId = self::RESCHEDULED_NOTIFY_CUSTOMER_EMAIL_TEMPLATE;
                    break;
            }
            $sender = [
                'name' => $name,
                'email' => $email,
            ];
            $vars = $templatevariable['vars'];
            /* email template */
            $template = $this->scopeConfig->getValue(
                $templateId,
                ScopeInterface::SCOPE_STORE,
                $storeId
            );

            $transport = $this->transportBuilder->setTemplateIdentifier(
                $template
            )->setTemplateOptions(
                [
                    'area' => Area::AREA_FRONTEND,
                    'store' => $this->getStoreId()
                ]
            )->setTemplateVars(
                $vars
            )->setFrom(
                $sender
            )->addTo(
                $templatevariable['email']
            )->getTransport();

            $transport->sendMessage();

        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }
        $this->inlineTranslation->resume();

        return $this;
    }

    /*
     * get Current store id
     */
    public function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    /*
     * get Current store Info
     */
    public function getStore()
    {
        return $this->storeManager->getStore();
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
    * send cancel sms
    */
    public function sendCancelSms($cancelVar)
    {
        $authkey = $this->getMsgauthkey();
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $smstemplateid = $this->scopeConfig->getValue(''.$cancelVar['templateid'].'', $storeScope);
        $senderName = $this->scopeConfig ->getValue('msggateway/servicesbookingemail/cancel_booking_sms_sender_name', $storeScope);
        $mobileNo = str_replace(' ', '', $cancelVar['mobileNo']);
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
            }
        }
        return true;
    }

    /*
    * send reschedule sms
    */
    public function sendRescheduleSms($scheduleVar)
    {
        $authkey = $this->getMsgauthkey();
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $smstemplateid = $this->scopeConfig->getValue(''.$scheduleVar['templateid'].'', $storeScope);
        $senderName = $this->scopeConfig ->getValue('msggateway/servicesbookingemail/reschedule_sms_sender_name', $storeScope);
        $mobileNo = str_replace(' ', '', $scheduleVar['mobileNo']);
        $path = "url";
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
                  CURLOPT_POSTFIELDS => "{\n  \"flow_id\": \"".$smstemplateid."\",\n  \"sender\": \"".$senderName."\",\n  \"mobiles\": \"".$mobileNo."\",\n  \"url\": \"".$path."\"\n}",
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
    * send assign/re-assign sms
    */
    public function sendAssignGroomerSms($assignVar)
    {
        $authkey = $this->getMsgauthkey();
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $smstemplateid = $this->scopeConfig->getValue(''.$assignVar['templateid'].'', $storeScope);
        $senderName = $this->scopeConfig ->getValue('msggateway/servicesbookingemail/assign_groomer_sms_sender_name', $storeScope);
        $mobileNo = str_replace(' ', '', $assignVar['mobileNo']);
        $pet = $assignVar['pet'];
        $groomer = $assignVar['groomer'];
        $path = "https://bit.ly/3lbaM68";
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
                  CURLOPT_POSTFIELDS => "{\n  \"flow_id\": \"".$smstemplateid."\",\n  \"sender\": \"".$senderName."\",\n  \"mobiles\": \"".$mobileNo."\",\n  \"plan\": \"".$pet."\",\n  \"groomer\": \"".$groomer."\",\n  \"url\": \"".$path."\"\n}",
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
    * send assign/re-assign sms to groomer
    */
    public function sendGroomerAssignedSms($assignedVar)
    {
        $authkey = $this->getMsgauthkey();
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $smstemplateid = $this->scopeConfig->getValue(''.$assignedVar['templateid'].'', $storeScope);
        $senderName = $this->scopeConfig ->getValue('msggateway/servicesbookingemail/assign_groomer_sms_sender_name', $storeScope);
        $mobileNo = str_replace(' ', '', $assignedVar['mobileNo']);
        $pet = $assignedVar['pet'];
        $id = $assignedVar['id'];
        $date = $assignedVar['date'];
        $time = $assignedVar['time'];
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
                  CURLOPT_POSTFIELDS => "{\n  \"flow_id\": \"".$smstemplateid."\",\n  \"sender\": \"".$senderName."\",\n  \"mobiles\": \"".$mobileNo."\",\n  \"pet\": \"".$pet."\",\n  \"id\": \"".$id."\",\n  \"date\": \"".$date."\",\n  \"time\": \"".$time."\"\n}",
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
    * send booking status to customer sms
    */
    public function sendBookingStatusSms($statusVar)
    {
        $authkey = $this->getMsgauthkey();
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $smstemplateid = $this->scopeConfig->getValue(''.$statusVar['templateid'].'', $storeScope);
        $senderName = $this->scopeConfig ->getValue('msggateway/servicesbookingemail/update_booking_status_sender_name', $storeScope);
        $mobileNo = str_replace(' ', '', $statusVar['mobileNo']);
        $petName = $statusVar['pet_name'];
        $bookingId = $statusVar['booking_id'];
        $status = $statusVar['status'];
        $path = "https://bit.ly/2W3FKUS";
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
                  CURLOPT_POSTFIELDS => "{\n  \"flow_id\": \"".$smstemplateid."\",\n  \"sender\": \"".$senderName."\",\n  \"mobiles\": \"".$mobileNo."\",\n  \"petname\": \"".$petName."\",\n  \"bookingid\": \"".$bookingId."\",\n  \"status\": \"".$status."\",\n  \"url\": \"".$path."\"\n}",
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
