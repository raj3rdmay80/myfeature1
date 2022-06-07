<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Sales
 */
declare(strict_types=1);

namespace Zigly\Sales\Block\Order;

use Magento\Framework\App\ObjectManager;
use Magento\Review\Model\ReviewFactory;
use Magento\Customer\Model\SessionFactory;
use Magento\Sales\Model\Order\ItemFactory;
use Magento\Directory\Model\CurrencyFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Sales\Model\ResourceModel\Order\Address\CollectionFactory;

class History extends \Magento\Framework\View\Element\Template
{

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * Constructor
     * @param Context $context
     * @param ItemFactory $itemFactory
     * @param TimezoneInterface $timezone
     * @param ReviewFactory $reviewFactory
     * @param SessionFactory $customerSession
     * @param CurrencyFactory $currencyFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param CollectionFactory $collectionFactory
     * @param PriceCurrencyInterface $priceCurrency
     * @param OrderRepositoryInterface $orderRepository
     * @param ProductRepositoryInterface $productrepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        ItemFactory $itemFactory,
        TimezoneInterface $timezone,
        ReviewFactory $reviewFactory,
        SessionFactory $customerSession,
        CurrencyFactory $currencyFactory,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        CollectionFactory $collectionFactory,
        PriceCurrencyInterface $priceCurrency,
        OrderRepositoryInterface $orderRepository,
        SerializerInterface $serializer,
        ProductRepositoryInterface $productrepository,
        array $data = []
    ) {
        $this->timezone = $timezone;
        $this->scopeConfig = $scopeConfig;
        $this->itemFactory = $itemFactory;
        $this->storeManager = $storeManager;
        $this->reviewFactory = $reviewFactory;
        $this->priceCurrency =  $priceCurrency;
        $this->currencyFactory = $currencyFactory;
        $this->orderRepository = $orderRepository;
        $this->customerSession = $customerSession;
        $this->collectionFactory = $collectionFactory;
        $this->serializer = $serializer;
        $this->productrepository = $productrepository;
        parent::__construct($context, $data);
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
    * get order product image
    */
    Public function getProductImage($id)
    {
        try {
            $product = $this->productrepository->getById($id);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e){
            $product = false;
        }
        if (!empty($product)) {
            $productImageUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA ).'catalog/product'.$product->getImage();
        } else {
            $productImageUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA ).'catalog/product/placeholder/'.$this->getConfig('catalog/placeholder/thumbnail_placeholder');
        }
        return $productImageUrl;
    }

    public function getConfig($config_path)
    {
        return $this->storeManager->getStore()->getConfig($config_path);
    }

    /*
    * get order product url
    */
    Public function getProductUrl($id)
    {
        $productUrl = '';
        try {
            $product = $this->productrepository->getById($id);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e){
            $product = false;
        }
        if (!empty($product)) {
            $productUrl = $product->getProductUrl();
        }
        return $productUrl;
    }

    /*
    * set date format
    */
    public function getDate($date)
    {
        return $this->timezone->date(new \DateTime($date))->format('d M, Y');
    }

    /**
     * Get order view URL
     *
     * @param object $order
     * @return string
     */
    public function getViewUrl($order)
    {
        return $this->getUrl('sales/orders/view', ['order_id' => $order->getId()]);
    }

    /*
    * get order status
    */
    public function getOrderStatus($orderId)
    {
        $order = $this->orderRepository->get($orderId);
        return $order->getStatus();
    }

    /**
     * @return address string
     */
    public function getAddressDetails($addressId)
    {
        $address = false;
        if ($addressId) {
            $shippingAddress = $this->collectionFactory->create()->addFieldToFilter('entity_id',array($addressId))->getFirstItem();
            if (!empty($shippingAddress)) {
                $street = $shippingAddress->getStreet();
                $address = $street['0'].", ";
                if (isset($street['1'])) {
                    $address .= $street['1'].", ";
                }
                $address .= $shippingAddress->getCity().", ".$shippingAddress->getRegion()." - ". $shippingAddress->getPostcode();
            }
        }
        return $address;
    }

    /*
    * get review show hide
    */
    public function reviewenable($productId)
    {
        $customerId = $this->customerSession->create()->getCustomer()->getId();
        $orderitems = $this->itemFactory->create()->getCollection()->addFieldToFilter('main_table.product_id',$productId);
        $orderitems->getSelect()->joinLeft(['order'=>'sales_order'],"main_table.order_id = order.entity_id",['ordercustomer_id' => 'order.customer_id'])->where("order.customer_id = '".$customerId."'");
        $collection = $this->reviewFactory->create()->getCollection()->addFieldToFilter('main_table.entity_pk_value',$productId)->addFieldToFilter('main_table.entity_id',1)->addFieldToFilter('detail.customer_id',$customerId);
        if($orderitems->getSize() && !$collection->getSize())
        {
            return true;
        }
        return false;
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

    public function unserialized($data)
    {
        return $this->serializer->unserialize($data);
    }
}
