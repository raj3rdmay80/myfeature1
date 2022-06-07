<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Login
 */
namespace Zigly\Login\Block\Customer;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\UrlInterface;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\View\Element\Template;

class Account extends Template
{
    protected $urlBuilder;
    protected $customerSession;
    protected $storeManager;
    protected $customerModel;

    public function __construct(
        Context $context,
        UrlInterface $urlBuilder,
        SessionFactory $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Customer $customerModel,
        array $data = []
    )
    {
        $this->urlBuilder = $urlBuilder;
        $this->customerSession = $customerSession->create();
        $this->storeManager = $storeManager;
        $this->customerModel = $customerModel;
        parent::__construct($context, $data);
        $collection = $this->getContracts();
        $this->setCollection($collection);
    }
    public function getBaseUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl();
    }

    public function getConfig($config_path)
    {
            return $this->storeManager->getStore()->getConfig($config_path);
    }
    public function getMediaUrl()
    {
        return $this->getBaseUrl() . 'media/';
    }

    public function getCustomerImageUrl($filePath)
    {
        return $this->getMediaUrl() . 'customer' . $filePath;
    }

    public function getFileUrl()
    {
        $customerData = $this->customerModel->load($this->customerSession->getId());
        $url = $customerData->getData('customerprofile_image');
        if (!empty($url)) {
            return $this->getCustomerImageUrl($url);
        }
        return false;
    }

    public function getplaceholderimageurl(){
       $path = 'catalog/placeholder/image_placeholder';
       $mediaUrl = $this ->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
       return $mediaUrl . 'catalog/product/placeholder/'.$this->getConfig($path);
    }
}
