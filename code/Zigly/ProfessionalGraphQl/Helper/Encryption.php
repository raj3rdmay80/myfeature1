<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_ProfessionalGraphQl
 */
declare(strict_types=1);

namespace Zigly\ProfessionalGraphQl\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;
use Zigly\Groomer\Model\GroomerFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
* For professionals authentication
*/
class Encryption extends AbstractHelper
{

    /**
    * MSG91 authkey config path
    */
    const XML_PATH_MSG_AUTHKEY = 'msggateway/general/authkey';

    /**
    * @var ScopeConfigInterface
    */
    protected $scopeConfig;

    /** @var GroomerFactory */
    protected $groomerFactory;

    /**
     * @param GroomerFactory $groomerFactory
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct (
        GroomerFactory $groomerFactory,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->groomerFactory = $groomerFactory;
    }

    /**
     * To decrypt & Authentication the professional token by id and timestamp 
     * @return []
     */
    public function tokenAuthentication($token)
    {
        $ciphering = "AES-128-CTR";
        $iv_length = openssl_cipher_iv_length($ciphering);
        $options = 0;
        $decryption_iv = '1234567891022222';
        $decryption_key = "inzigly";
        $decryption = openssl_decrypt ($token, $ciphering, $decryption_key, $options, $decryption_iv);
        if ($decryption) {
            $decryptData = explode(":", $decryption);
            if (count($decryptData) && !empty($decryptData[0]) && !empty($decryptData[1])) {
                $professional = $this->groomerFactory->create()->load($decryptData[0]);
                if ($professional && !empty($professional->getApiToken())) {
                    if($decryptData[1] == $professional->getApiTokenCreatedAt()) {
                        $now = new \DateTime('now', new \DateTimeZone('Asia/Kolkata'));
                        $currentTimeStamp = $now->getTimestamp() + $now->getOffset();
                        if($currentTimeStamp - (int)$decryptData[1] > 604800){
                            $professional->setApiToken(Null)->save();
                            return false;
                        }
                        return $professional;
                    }
                }
            }
        }
        return false;
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
    * send service provider detail submission sms & profile approved
    */
    public function sendProfessionalSms($serviceVar)
    {
        $authkey = $this->getMsgauthkey();
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $smstemplateid = $this->scopeConfig->getValue(''.$serviceVar['templateid'].'', $storeScope);
        $senderName = $this->scopeConfig ->getValue('status_notify/status/professional_status_sender_name', $storeScope);
        $mobileNo = trim($serviceVar['mobileNo']);
        $mobileNo = '91'.$mobileNo;
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
                  CURLOPT_POSTFIELDS => "{\n  \"flow_id\": \"".$smstemplateid."\",\n  \"sender\": \"".$senderName."\",\n  \"mobiles\": \"".$mobileNo."\"\n}",
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