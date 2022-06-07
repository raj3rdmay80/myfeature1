<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_GroomingReview
 */
declare(strict_types=1);

namespace Zigly\GroomerReview\Helper;

use Magento\Framework\App\Area;
use Magento\Framework\Escaper;
use Magento\Framework\Registry;
// use Zigly\Login\Model\OtpreportFactory;
// use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\App\Helper\Context;
// use Magento\Customer\Model\SessionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Helper\AbstractHelper;
// use Magento\Framework\Serialize\SerializerInterface;
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
    * @var storeManager
    */
    protected $storeManager;

    /**
    * MSG91 authkey config path
    */
    // const XML_PATH_MSG_AUTHKEY = 'msggateway/general/authkey';

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
        // Curl $curl,
        Context $context,
        // Escaper $escaper,
        // Registry $registry,
        // SerializerInterface $serializer,
        // SessionFactory $customerSession,
        ScopeConfigInterface $scopeConfig,
        StateInterface $inlineTranslation,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager
        // OtpreportFactory $otpreportFactory
    ) {
        // $this->curl = $curl;
        // $this->escaper = $escaper;
        // $this->registry = $registry;
        // $this->serializer = $serializer;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        // $this->customerSession = $customerSession;
        // $this->otpreportFactory = $otpreportFactory;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        parent::__construct($context);
    }

    /*
    * get auth key
    */
    // public function getMsgauthkey()
    // {
    //     $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
    //     return $this->scopeConfig->getValue(self::XML_PATH_MSG_AUTHKEY, $storeScope);
    // }

    /*
    * send email function
    */
    public function sendMail($templatevariable, $data)
    {
        $this->inlineTranslation->suspend();
        try {
            $storeId = $this->storeManager->getStore()->getId();
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $email = $this->scopeConfig->getValue('trans_email/ident_sales/email', $storeScope, $storeId);
            $name = $this->scopeConfig->getValue('trans_email/ident_sales/name', $storeScope, $storeId);
            $templateid = $this->scopeConfig->getValue(''.$templatevariable['template_id'].'', $storeScope, $storeId);
            $admin_email = $this->scopeConfig->getValue('groomerreview/groomerreviewemail/send_email_id', $storeScope, $storeId);
            $sender = [
                'name' => $name,
                'email' => $email,
            ];

            if (!empty($templateid) && !empty($templatevariable['email'])){
                $admin_email = explode(",", $admin_email);
                $transport = $this->transportBuilder
                    ->setTemplateIdentifier($templateid)
                    ->setTemplateOptions(
                        [
                            'area' => Area::AREA_FRONTEND,
                            'store' => $storeId,
                        ]
                    )
                    ->setTemplateVars(['customer_name' => ''.$templatevariable['customer_name'].'',
                                        'star_rating' => $data['star_rating'],
                                        'tag_name' => ''.$data['tag_name'].'',
                                         ])
                    ->setFrom($sender)
                    ->addTo(array_merge($admin_email, $templatevariable['email']))
                    ->addBcc($admin_email)
                    ->getTransport();
                $transport->sendMessage();
            }
        } catch (\Exception $e) {
            // $this->messageManager->addError($e->getMessage());
        }
        $this->inlineTranslation->resume();
        return $this;
    }

}
