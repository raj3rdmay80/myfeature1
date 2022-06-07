<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Sales
 */
declare(strict_types=1);

namespace Zigly\Sales\Block\Adminhtml\Order;

use Magento\Backend\Model\Url;
use Magento\Framework\App\ObjectManager;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class ModalBox
 *
 * @package Zigly\Sales\Block\Adminhtml\Order
 */
class ModalBox extends \Magento\Backend\Block\Template
{

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Constructor
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        Url $backendUrlManager,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->backendUrlManager  = $backendUrlManager;
        parent::__construct($context, $data);
    }


    public function getCancelUrl()
    {
        return $this->backendUrlManager->getUrl('sales/orders/cancel');
    }

    public function getReturnUrl()
    {
        return $this->backendUrlManager->getUrl('sales/orders/returned');
    }

    public function getFrontendUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl();
    }

    /*
    * get order cancel reason
    */
    public function getOrderCancelReason()
    {
        $value = $this->scopeConfig->getValue('order/order_cancel_reason_config/cancelreasons', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (empty($value)) {
            return false;
        }
        if ($this->isSerialized($value)) {
            $unserializer = ObjectManager::getInstance()->get(\Magento\Framework\Unserialize\Unserialize::class);
        } else {
            $unserializer = ObjectManager::getInstance()->get(\Magento\Framework\Serialize\Serializer\Json::class);
        }
        $data = $unserializer->unserialize($value);
        $reason = [];
        foreach ($data as $key => $reas) {
            $reason[] = $reas['cancelreason'];
        }
        return $reason;
    }

    /*
    * get order return reason
    */
    public function getOrderReturnReason()
    {
        $value = $this->scopeConfig->getValue('order/order_cancel_reason_config/returnreasons', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (empty($value)) {
            return false;
        }
        if ($this->isSerialized($value)) {
            $unserializer = ObjectManager::getInstance()->get(\Magento\Framework\Unserialize\Unserialize::class);
        } else {
            $unserializer = ObjectManager::getInstance()->get(\Magento\Framework\Serialize\Serializer\Json::class);
        }
        $data = $unserializer->unserialize($value);
        $reason = [];
        foreach ($data as $key => $reas) {
            $reason[] = $reas['returnreason'];
        }
        return $reason;
    }

    /**
     * Check if value is a serialized string
     *
     * @param string $value
     * @return boolean
     */
    private function isSerialized($value)
    {
        return (boolean) preg_match('/^((s|i|d|b|a|O|C):|N;)/', $value);
    }
}
