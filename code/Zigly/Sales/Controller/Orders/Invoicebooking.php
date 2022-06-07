<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Sales
 */

namespace Zigly\Sales\Controller\Orders;

use Magento\Sales\Model\Order;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;
use Zigly\GroomingService\Model\GroomingFactory;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Invoicebooking extends \Magento\Framework\App\Action\Action
{

    /**
     * @var Curl
     */
    protected $curl;

    /**
     * @var PageFactory
    */
    protected $pageFactory;

    /**
     * @param Curl $curl
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param PageFactory $resultPageFactory
     * @param SessionFactory $customerSession
     * @param SerializerInterface $serializer
     * @param GroomingFactory $groomingFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Curl $curl,
        Context $context,
        PageFactory $pageFactory,
        SerializerInterface $serializer,
        JsonFactory $resultJsonFactory,
        SessionFactory $customerSession,
        GroomingFactory $groomingFactory,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->curl = $curl;
        $this->serializer = $serializer;
        $this->scopeConfig = $scopeConfig;
        $this->pageFactory = $pageFactory;
        $this->storeManager = $storeManager;
        $this->groomingFactory = $groomingFactory;
        $this->customerSession = $customerSession;
        $this->resultJsonFactory = $resultJsonFactory;
        return parent::__construct($context);
    }

    public function execute()
    {
        $resultPage = $this->pageFactory->create();
        $result = $this->resultJsonFactory->create();
        $post = $this->getRequest()->getPostValue();
        $bookingId = $post['bookingId'];
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/response.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info(print_r("--------Start--------", true));
        $responseData = [
            'success' => false,
            'message' => 'Something went wrong. Please reload and try again'
        ];
        if (empty($bookingId)) {
            $responseData['message'] = 'Something went wrong. Please reload and try again';
            $result->setData($responseData);
            return $result;
        }
        $booking = $this->groomingFactory->create()->load($bookingId);
        $logger->debug(var_export($booking->getIncrementId(), true));
        $currentCustomer = $this->customerSession->create()->getCustomer();
        try {
            if ($currentCustomer->getId() == $booking->getCustomerId()) {
                $status = ['canceled', 'pending'];
                if (!in_array($booking->getStatus(), $status)) {
                    $storeId = $this->storeManager->getStore()->getId();
                    $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                    $userName = $this->scopeConfig->getValue('brown_tape/api_config/username', $storeScope, $storeId);
                    $auth = $this->scopeConfig->getValue('brown_tape/api_config/auth', $storeScope, $storeId);
                    $channelId = $this->scopeConfig->getValue('brown_tape/api_config/channel_id', $storeScope, $storeId);
                    $incrementId = $booking->getIncrementId();
                    if ($userName && $auth && $channelId && $incrementId) {
                        $curlUrl = 'https://app.browntape.com/0.1/orders/getdocs.json?username='.$userName.'&auth_string='.$auth.'&channel_id='.$channelId.'&order_references[]='.$incrementId.'';
                        $curl = $this->curl;
                        $err = $curl->get($curlUrl, []);
                        $response = $curl->getBody();
                        $serialized = $this->serializer->unserialize($response);
                        $logger->debug(var_export($serialized, true));
                        $logger->info(print_r("--------End--------", true));
                        if ($err) {
                            $responseData['message'] = "cURL Error #:" . $err;
                            $result->setData($responseData);
                            return $result;
                        }
                        if ($serialized['success']){
                            $responseData['success'] = true;
                            $responseData['message'] = '';
                            $responseData['invoice'] = $serialized['data'][0]['Order']['bt_invoice'];
                        } else {
                            $responseData['success'] = false;
                            $responseData['message'] = 'Something went wrong. Please reload and try again';
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
        }
        $result->setData($responseData);
        return $result;
    }
}