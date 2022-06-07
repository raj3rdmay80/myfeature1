<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Sales
 */
declare(strict_types=1);

namespace Zigly\Sales\Block\Order;

use Zigly\Plan\Model\PlanFactory;
use Magento\Framework\App\Request\Http;
use Magento\Customer\Model\SessionFactory;
use Magento\Customer\Model\AddressFactory;
use Magento\Store\Model\StoreManagerInterface;
use Zigly\GroomingService\Model\GroomingFactory;
use Magento\LoginAsCustomerApi\Api\ConfigInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Zigly\GroomingService\Model\Grooming as GroomingStatus;
use Zigly\Groomer\Model\GroomerFactory as ProfessionalFactory;
use Magento\LoginAsCustomerApi\Api\GetLoggedAsCustomerAdminIdInterface;
use Zigly\GroomingService\Model\ResourceModel\Grooming\CollectionFactory;
use Zigly\GroomerReview\Model\ResourceModel\GroomerReview\CollectionFactory as GroomerReviewCollectionFactory;
use Magento\LoginAsCustomerApi\Api\IsLoginAsCustomerSessionActiveInterface;
use Zigly\ReviewTag\Model\ResourceModel\ReviewTag\CollectionFactory as ReviewTagCollectionFactory;

class Booking extends \Magento\Framework\View\Element\Template
{

    /**
     * @var $request
     */
    protected $request;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var $collectionFactory
     */
    protected $collectionFactory;

    /**
     * @var $timezoneInterface
     */
    protected $timezoneInterface;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var AddressFactory
     */
    protected $address;

    /**
     * @var GetLoggedAsCustomerAdminIdInterface
     */
    private $getLoggedAsCustomerAdminId;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var IsLoginAsCustomerSessionActiveInterface
     */
    private $isLoginAsCustomerSessionActive;

    /**
     * @var ReviewTagCollectionFactory
     */
    private $reviewTagCollectionFactory;

    /**
     * Constructor
     * @param array $data
     * @param Http $request
     * @param Context $context
     * @param PlanFactory $planFactory
     * @param ConfigInterface $config
     * @param AddressFactory $address
     * @param SessionFactory $customer
     * @param GroomingFactory $groomingFactory
     * @param CustomerSession $customerSession
     * @param StoreManagerInterface $storemanager
     * @param CollectionFactory $collectionFactory
     * @param TimezoneInterface $timezoneInterface
     * @param PriceCurrencyInterface $priceCurrency
     * @param ProfessionalFactory $professionalFactory
     * @param GroomerReviewCollectionFactory $groomerReviewCollectionFactory
     * @param GetLoggedAsCustomerAdminIdInterface $getLoggedAsCustomerAdminId
     * @param ReviewTagCollectionFactory $reviewTagCollectionFactory
     */
    public function __construct(
        Http $request,
        Context $context,
        ConfigInterface $config,
        AddressFactory $address,
        SessionFactory $customer,
        PlanFactory $planFactory,
        CustomerSession $customerSession,
        GroomingFactory $groomingFactory,
        StoreManagerInterface $storemanager,
        CollectionFactory $collectionFactory,
        TimezoneInterface $timezoneInterface,
        PriceCurrencyInterface $priceCurrency,
        ProfessionalFactory $professionalFactory,
        GroomerReviewCollectionFactory $groomerReviewCollectionFactory,
        GetLoggedAsCustomerAdminIdInterface $getLoggedAsCustomerAdminId,
        IsLoginAsCustomerSessionActiveInterface $isLoginAsCustomerSessionActive,
        ReviewTagCollectionFactory $reviewTagCollectionFactory,
        array $data = []
    ) {
        $this->config = $config;
        $this->request = $request;
        $this->address = $address;
        $this->customer = $customer;
        $this->plan = $planFactory;
        $this->storeManager = $storemanager;
        $this->priceCurrency = $priceCurrency;
        $this->customerSession = $customerSession;
        $this->groomingFactory = $groomingFactory;
        $this->collectionFactory = $collectionFactory;
        $this->timezoneInterface = $timezoneInterface;
        $this->professionalFactory = $professionalFactory;
        $this->getLoggedAsCustomerAdminId = $getLoggedAsCustomerAdminId;
        $this->groomerReviewCollectionFactory = $groomerReviewCollectionFactory;
        $this->isLoginAsCustomerSessionActive = $isLoginAsCustomerSessionActive;
        $this->reviewTagCollectionFactory = $reviewTagCollectionFactory;
        parent::__construct($context, $data);
    }

    /*
    * get upcoming booking
    */
    Public function getUpcomingBooking()
    {
        $customerId = $this->customer->create()->getCustomer()->getId();
        $upcomingCollection = [];
        if ($customerId) {
            $now = new \DateTime('now', new \DateTimeZone('Asia/Kolkata'));
            $currentTimeStamp = $now->getTimestamp() + $now->getOffset();
            $upcomingCollection = $this->collectionFactory->create()
                                ->addFieldToFilter('visibility', ['eq' => 2])
                                ->addFieldToFilter('customer_id', ['in' => $customerId])
                                ->addFieldToFilter('scheduled_timestamp', ['gteq' => $currentTimeStamp])
                                ->addFieldToFilter('booking_status', ['neq' => 'Completed'])
                                ->setOrder('entity_id', 'DESC');
            if($this->isLoginAsCustomer()) {
                $upcomingCollection->addFieldToFilter('center', ['eq' => 'At Experience Center'])
                                   ->addFieldToFilter('booking_type', ['eq' => 1]);
            }
        }
        return $upcomingCollection;
    }

    /*
    * get past booking
    */
    Public function getPastBooking()
    {
        $customerId = $this->customer->create()->getCustomer()->getId();
        $pastCollection = [];
        if ($customerId) {
            $now = new \DateTime('now', new \DateTimeZone('Asia/Kolkata'));
            $currentTimeStamp = $now->getTimestamp() + $now->getOffset();
            $pastCollection = $this->collectionFactory->create()
                                ->addFieldToFilter('visibility', ['eq' => 2])
                                ->addFieldToFilter('customer_id', ['in' => $customerId])
                                ->addFieldToFilter('scheduled_timestamp', ['lteq' => $currentTimeStamp])
                                ->setOrder('entity_id', 'DESC');
            if($this->isLoginAsCustomer()) {
                $pastCollection->addFieldToFilter('center', ['eq' => 'At Experience Center'])
                               ->addFieldToFilter('booking_type', ['eq' => 1]);
            }
        }
        return $pastCollection;
    }

    /**
     * Get Plan image
     */
    public function getPlanImage($image)
    {
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
        if (!empty($image)) {
            $imageurl = $mediaUrl."plan/feature/".$image;
        } else {
            $imageurl = $mediaUrl.'catalog/product/placeholder/'.$this->getConfig('catalog/placeholder/thumbnail_placeholder');
        }
        return $imageurl;
    }

    public function getConfig($config_path)
    {
        return $this->storeManager->getStore()->getConfig($config_path);
    }

    /**
     * Get Plan collection based on petDetails
     * @return CollectionFactory
     */
    public function getPlans($id)
    {
        $selectedPlan = false;
        if ($id) {
            $plan = $this->plan->create()->load($id);
            if (!empty($plan)) {
                $selectedPlan = $plan;
            }
        }
        return $selectedPlan;
    }

    /**
     * Check if Logged as customer
     *
     * @return  bool|null
     */
    public function isLoginAsCustomer()
    {
        $adminId = $this->getLoggedAsCustomerAdminId->execute();
        $customerId = (int)$this->customerSession->getCustomerId();
        if ($this->config->isEnabled() && $adminId && $customerId && $this->isLoginAsCustomerSessionActive->execute($customerId, $adminId)) {
            return true;
        }
        return false;
    }

    /*
    * set date format
    */
    public function getDate($date)
    {
        return $this->timezoneInterface->date(new \DateTime($date))->format('d M \'y');
    }

    /*
    * get booking details by id
    */
    public function getBookingById()
    {
        $bookingId = $this->request->getParam('booking_id');
        $bookingDetails = $this->groomingFactory->create()->load($bookingId);
        return $bookingDetails;
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
    * get professional by id
    */
    public function getProfessionalById($id)
    {
        $professional = $this->professionalFactory->create()->load($id);
        return $professional;
    }

    /*
    * get professional image
    */
    public function getProfessionalimage($image)
    {
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
        $imageurl = $mediaUrl."groomer/feature/".$image;
        return $imageurl;
    }

    /**
     * Get booking status
     *
     * @param   string $status
     * @return  string
     */
    public function getStatus($status)
    {
        $serviceStatus = [
            GroomingStatus::STATUS_CANCELLED_BY_ADMIN => __('Cancelled'),
            GroomingStatus::STATUS_CANCELLED_BY_CUSTOMER => __('Cancelled'),
            GroomingStatus::STATUS_CANCELLED_BY_PROFESSIONAL => __('Cancelled'),
            GroomingStatus::STATUS_RESCHEDULED_BY_ADMIN => __('Rescheduled'),
            GroomingStatus::STATUS_RESCHEDULED_BY_CUSTOMER => __('Rescheduled'),
            GroomingStatus::STATUS_RESCHEDULED_BY_PROFESSIONAL => __('Rescheduled'),
        ];
        return (isset($serviceStatus[$status])) ? $serviceStatus[$status] : $status;
    }

    /**
     * Get review tag
     *
     * @return  array
     *
     */
    public function getReviewTagCollection()
    {
        $reviewCollectionFactory = $this->reviewTagCollectionFactory->create()->addFieldToSelect('tag_name')->addFieldToFilter('is_active','1')->setOrder('reviewtag_id','ASC');
        return $reviewCollectionFactory;
    }

    /**
     * Get review status and service status
     *
     * @return  array
     *
     */
    public function getReviewStatus()
    {
        $reviewStatusCollection = [];
        $bookingId = $this->getRequest()->getParam('booking_id');
        $reviewStatusCollection = $this->collectionFactory->create()->addFieldToSelect('entity_id')->addFieldToSelect('booking_status')->addFieldToSelect('review')->addFieldToSelect('groomer_id')->addFieldToFilter('entity_id',"$bookingId");
        $reviewStatus = $reviewStatusCollection->getData();
        $reviewStatus[0]['booking_id'] = $bookingId;
        return $reviewStatus;
    }
    /**
     * Get booking id param value
     *
     * @return integer
     *
     */
    public function getBookingId()
    {
        return $this->request->getParam('booking_id');
    }


    /**
     * Get professional review by id
     *
     * @return integer
     *
     */
    public function getProfessionalReviewById($id)
    {
        $groomerReviewCollection = $this->groomerReviewCollectionFactory->create()->addFieldToFilter('main_table.is_active','1')->addFieldToFilter('main_table.groomer_id', ['eq' => $id])->setPageSize(10)->setOrder('main_table.groomerreview_id','DESC');
        $groomerReviewCollection->getSelect()->join(['groomer'=>'zigly_service_grooming'],"main_table.service_id = groomer.entity_id",['scheduled_date' => 'groomer.scheduled_date']);
        $groomerReviewCollection->getSelect()->join(['customer'=>'customer_entity'],"groomer.customer_id = customer.entity_id",['customer_name' => 'customer.firstname']);
        return $groomerReviewCollection;
    }

    /**
     * Get Date formatted.
     *
     * @param string $datetime
     * @param string $full
     * @return string
     */
    public function reviewedAtFormat($datetime, $full = false)
    {
        $now = new \DateTime;
        $ago = new \DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }

}

