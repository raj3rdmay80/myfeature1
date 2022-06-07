<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Cityscreen
 */
declare(strict_types=1);

namespace Zigly\Cityscreen\Controller\Cityscreen;

class Savecityscreen extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface CookieManagerInterface
     */
    private $cookieManager;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory CookieMetadataFactory
     */
    private $cookieMetadataFactory;

    /**
     * @var JsonFactory
     */
    protected $jsonResultFactory;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
    ) {
        $this->cookieManager = $cookieManager;
        $this->jsonResultFactory = $jsonResultFactory;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        parent::__construct($context);
    }

    /**
     * Execute save action
     * @return void
     */
    public function execute()
    {
        $post = $this->getRequest()->getPostValue();
        try {
            $searched = $post['searched'];
            $city = preg_replace('/[,0-9]+/', '', $searched);
            $city = trim($city);
            if ($city == 'नई दिल्ली') {
                $city = 'New Delhi';
            }
            $pincode = preg_replace('/[,A-Za-z]+/', '', $searched);
            $pincode = str_replace(' ', '', $pincode);
            $publicCookieMetadata = $this->cookieMetadataFactory->createPublicCookieMetadata();
            $publicCookieMetadata->setDuration('86400');
            $publicCookieMetadata->setPath('/');
            $publicCookieMetadata->setHttpOnly(false);
            $this->cookieManager->setPublicCookie('city_screen', $city, $publicCookieMetadata);
            $this->cookieManager->setPublicCookie('pincode_check', $pincode, $publicCookieMetadata);
            $this->cookieManager->deleteCookie('glatlng', $publicCookieMetadata);
            $this->cookieManager->deleteCookie('street1', $publicCookieMetadata);
            $this->cookieManager->deleteCookie('street2', $publicCookieMetadata);
            $this->cookieManager->deleteCookie('state', $publicCookieMetadata);
        } catch (\Exception $e) {
            /* print_r($e->getMessage()); */
        }
        $result = $this->jsonResultFactory->create();
        $result->setData(['output' => $post['searched']]);
        return $result;
    }
}