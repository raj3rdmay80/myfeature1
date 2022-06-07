<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Wallet
 */
declare(strict_types=1);

namespace Zigly\Wallet\Helper;

use Magento\Framework\Escaper;
use Magento\Framework\App\Area;
use Magento\Framework\Registry;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\App\Helper\Context;
use Magento\Customer\Model\SessionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
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
    * Wallet percent  config path
    */
    const WALLLET_RULE_PERCENT_PATH = 'wallet/wallet_usage/max_transaction';

    /**
    * Wallet percent  config path
    */
    const WALLLET_IS_ENABLED = 'wallet/general/enabled';

    /**
    * Wallet enable for customer config path
    */
    const WALLET_IS_ENABLED_FOR_CUSTOMER = 'wallet/general/enabled_recharge';

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @param Curl $curl
     * @param Context $context
     * @param Escaper $escaper
     * @param Registry $registry
     * @param SessionFactory $customerSession
     * @param ScopeConfigInterface $scopeConfig
     * @param StateInterface $inlineTranslation
     * @param StoreManagerInterface $storeManager
     * @param PriceCurrencyInterface $priceCurrency
     * @param TransportBuilder $transportBuilder
     */
    public function __construct(
        Curl $curl,
        Context $context,
        Escaper $escaper,
        Registry $registry,
        SessionFactory $customerSession,
        ScopeConfigInterface $scopeConfig,
        StateInterface $inlineTranslation,
        TransportBuilder $transportBuilder,
        PriceCurrencyInterface $priceCurrency,
        StoreManagerInterface $storeManager
    ) {
        $this->curl = $curl;
        $this->escaper = $escaper;
        $this->registry = $registry;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->priceCurrency = $priceCurrency;
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

    /**
     * Convert and format price value for current application store
     *
     * @param   float $value
     * @param   bool $format
     * @param   bool $includeContainer
     * @return  float|string
     */
    public function currency($value, $format = true, $includeContainer = true)
    {
        return $format
            ? $this->priceCurrency->convertAndFormat($value, $includeContainer)
            : $this->priceCurrency->convert($value);
    }

    /*
    * get wallet money
    */
    public function getWalletMoney()
    {
        $customer = $this->customerSession->create()->getCustomer();
        return $totalBalance = is_null($customer->getWalletBalance()) ? "0" : $customer->getWalletBalance();
    }

    /*
    * get Transaction Percent
    */
    public function getMaxTransactionPercent()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::WALLLET_RULE_PERCENT_PATH, $storeScope);
    }

    /*
    * get Wallet is enabled
    */
    public function isEnabled()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::WALLLET_IS_ENABLED, $storeScope);
    }

    /*
    * get Wallet recharge is enabled
    */
    public function isWalletRechargeEnabled()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::WALLET_IS_ENABLED_FOR_CUSTOMER, $storeScope);
    }

    /*
    * Get Config
    */
    public function getConfig($path)
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue($path, $storeScope);
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
                    ['data' => $templatevariable['data']]
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
    * send recharge sms
    */
    public function sendRechargeSms($data)
    {
        $authkey = $this->getMsgauthkey();
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $smstemplateid = $this->scopeConfig->getValue(''.$data['templateid'].'', $storeScope);
        $senderName = $this->scopeConfig ->getValue('wallet/wallet_notification/wallet_recharge_sender_name', $storeScope);
        $mobileNo = trim($data['mobileNo']);
        $amount = $data['amount'];
        /*$mobileNo = '91'.$mobileNo;*/
        $mobileNo = '917358684501';
        $orderId = '';
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
                  CURLOPT_POSTFIELDS => "{\n  \"flow_id\": \"".$smstemplateid."\",\n  \"sender\": \"".$senderName."\",\n  \"mobiles\": \"".$mobileNo."\",\n  \"amount\": \"".$amount."\"\n,\n  \"orderid\": \"".$orderId."\"\n}",
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