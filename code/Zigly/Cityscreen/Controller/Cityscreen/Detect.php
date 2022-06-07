<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Cityscreen
 */
declare(strict_types=1);

namespace Zigly\Cityscreen\Controller\Cityscreen;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Zigly\Cityscreen\Model\ResourceModel\Cityscreen\CollectionFactory;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Customer\Model\SessionFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\CustomerFactory;

class Detect extends \Magento\Framework\App\Action\Action
{

    /**
     * @var CookieManagerInterface CookieManagerInterface
     */
    private $cookieManager;

    /**
     * @var CookieMetadataFactory CookieMetadataFactory
     */
    private $cookieMetadataFactory;

    /**
     * @var JsonFactory
     */
    protected $jsonResultFactory;

   /**
     * @var SessionFactory
     */
    protected $customerSession;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * Constructor
     *
     * @param Context  $context
     * @param CookieManagerInterface $cookieManager
     * @param JsonFactory $jsonResultFactory
     * @param SessionFactory $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     * @param CollectionFactory $cityscreenCollection
     * @param CookieMetadataFactory $cookieMetadataFactory
     */
    public function __construct(
        Context $context,
        CookieManagerInterface $cookieManager,
        JsonFactory $jsonResultFactory,
        SessionFactory $customerSession,
        CollectionFactory $cityscreenCollection,
        CustomerRepositoryInterface $customerRepository,
        CustomerFactory $customerFactory,
        CookieMetadataFactory $cookieMetadataFactory
    ) {
        $this->cookieManager = $cookieManager;
        $this->cityscreenCollection = $cityscreenCollection;
        $this->jsonResultFactory = $jsonResultFactory;
        $this->customerSession = $customerSession;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
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
        $city = $post['city'];
        if ($city == 'नई दिल्ली') {
            $city = 'New Delhi';
        }
        $pin = $post['postcode'];
        $responseData = ['success' => false, 'message' => 'Something went wrong'];
        try {
            /*$city = $this->cityscreenCollection->create()
                ->addFieldToFilter('city', ['eq' => $city])
                ->addFieldToFilter('pincode', ['eq' => $pin]);
            $city->getData();
            if (count($city)) {*/
                $publicCookieMetadata = $this->cookieMetadataFactory->createPublicCookieMetadata();
                $publicCookieMetadata->setDuration('86400');
                $publicCookieMetadata->setPath('/');
                $publicCookieMetadata->setHttpOnly(false);
                $this->cookieManager->setPublicCookie('city_screen', $city, $publicCookieMetadata);
                $this->cookieManager->setPublicCookie('pincode_check', $pin, $publicCookieMetadata);
                $this->cookieManager->setPublicCookie('street1', $post['address1'], $publicCookieMetadata);
                $this->cookieManager->setPublicCookie('street2', $post['address2'], $publicCookieMetadata);
                $this->cookieManager->setPublicCookie('state', $post['state'], $publicCookieMetadata);
                $this->cookieManager->setPublicCookie('glatlng', json_encode($post['latlng']), $publicCookieMetadata);
                if ($this->customerSession->create()->isLoggedIn()) {
                    $customerId = $this->customerSession->create()->getCustomerId();
                    $customer = $this->customerFactory->create()->load($customerId);
                    $customerData = $customer->getDataModel();
                    $customerData->setCustomAttribute('latlng', json_encode($post['latlng']));
                    $customer->updateData($customerData);
                    $this->customerFactory->create()->getResource()->saveAttribute($customer, 'latlng');
                }

                $responseData['success'] = true;
                $responseData['message'] = true;
            /*} else {
                $responseData['message'] = 'Can\'t find the city. please try Searching';
            }*/
        } catch (\Exception $e) {
            $responseData['message'] = 'Can\'t find the city. please try Searching';
            $responseData['trace'] = $e->getMessage();
        }
        $result = $this->jsonResultFactory->create();
        $result->setData($responseData);
        return $result;
    }
}