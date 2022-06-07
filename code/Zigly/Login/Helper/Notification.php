<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Login
 */
declare(strict_types=1);

namespace Zigly\Login\Helper;

use Magento\Framework\Escaper;
use Magento\Framework\App\Area;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;

class Notification extends AbstractHelper
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param Context $context
     * @param Escaper $escaper
     * @param StateInterface $inlineTranslation
     * @param ScopeConfigInterface $scopeConfig
     * @param TransportBuilder $transportBuilder
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        Escaper $escaper,
        ScopeConfigInterface $scopeConfig,
        StateInterface $inlineTranslation,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager
    ) {
        $this->escaper = $escaper;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        parent::__construct($context);
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
            $templateid = $templatevariable['template_id'];
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
            return ['message' => $e->getMessage()];
        }
        $this->inlineTranslation->resume();
        return $this;
    }

}