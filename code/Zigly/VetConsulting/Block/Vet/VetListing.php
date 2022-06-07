<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_VetConsulting
 */
declare(strict_types=1);

namespace Zigly\VetConsulting\Block\Vet;

use Magento\Framework\View\Element\Template\Context;
use Zigly\VetConsulting\Model\Session as VetSession;
use Magento\Customer\Model\Session;
use Zigly\Hub\Model\HubFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Zigly\Groomer\Model\ResourceModel\Groomer\CollectionFactory as GroomerCollectionFactory;
use Zigly\GroomerReview\Model\ResourceModel\GroomerReview\CollectionFactory as GroomerReviewCollectionFactory;

/**
 * Show plans for selection
 */
class VetListing extends \Magento\Framework\View\Element\Template
{

    /**
     * @var Session
     */
    protected $customer;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var VetSession
     */
    protected $vetSession;

    /**
     * @var vet session's value
     */
    protected $vetSessionData;

    /**
     * @var CookieManagerInterface
     */
    private $cookieManager;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Constructor
     * @param HubFactory $hubFactory
     * @param Session $customer
     * @param CollectionFactory $planCollection
     * @param PriceCurrencyInterface $priceCurrency
     * @param StoreManager $storeManager
     * @param StoreManagerInterface $storeManager
     * @param vetSession $groomingSession
     * @param GroomingCollectionFactory $groomingCollectionFactory
     * @param GroomerReviewCollectionFactory $groomerReviewCollectionFactory
     * @param Context $context
     * @param CookieManagerInterface $cookieManager
     * @param array $data
     */
    public function __construct(
        Session $customer,
        StoreManagerInterface $storeManager,
        PriceCurrencyInterface $priceCurrency,
        HubFactory $hubFactory,
        VetSession $vetSession,
        GroomerCollectionFactory $groomerCollectionFactory,
        CookieManagerInterface $cookieManager,
        GroomerReviewCollectionFactory $groomerReviewCollectionFactory,
        Context $context,
        array $data = []
    ) {
        $this->customer = $customer;
        $this->hubFactory = $hubFactory;
        $this->priceCurrency = $priceCurrency;
        $this->storeManager = $storeManager;
        $this->vetSession = $vetSession;
        $this->groomerCollectionFactory = $groomerCollectionFactory;
        $this->groomerReviewCollectionFactory = $groomerReviewCollectionFactory;
        $this->cookieManager = $cookieManager;
        parent::__construct($context, $data);
    }

    public function getConfig($config_path)
    {
        return $this->storeManager->getStore()->getConfig($config_path);
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

    /**
     * @return Grooming Session selected data
     */
    public function getVetSession()
    {
        if (!$this->vetSessionData) {
            $this->vetSessionData = $this->vetSession->getVet();
        }
        return $this->vetSessionData;
    }

    /**
     * @return Vets
     */
    public function getVets()
    {
        $specialty = ($this->getRequest()->getParam('specialty')) ? $this->getRequest()->getParam('specialty') : '';
        $sort = ($this->getRequest()->getParam('sort')) ? $this->getRequest()->getParam('sort') : '';
        $page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
        $pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 10;
        $vetProfessionals = $this->groomerCollectionFactory->create()
                                ->addFieldToFilter('professional_role', '3')
                                ->addFieldToFilter('status', '1')
                                ->setPageSize($pageSize)
                                ->setCurPage($page);
        $sessionData = $this->getVetSession();
        if (!empty($sessionData['selected_date']) && !empty($sessionData['selected_time'])) {
            $vetProfessionals->getSelect()->join(['scheduled'=>'zigly_schedulemanagement_schedulemanagement'],"(main_table.groomer_id = scheduled.professional_id AND scheduled.booking_id = 0 AND scheduled.availability = 1 )",['schedulemanagement_id' => 'scheduled.schedulemanagement_id']);
            $gmtTimezone = new \DateTimeZone('GMT');
            $myDateTime = new \DateTime($sessionData['selected_date']." ".$sessionData['selected_time'], $gmtTimezone);
            $vetProfessionals->addFieldToFilter('scheduled.slot_start_time', ['eq' => $myDateTime]);
            $vetProfessionals->addFieldToFilter('vet_service_center', '2');
        }else {
            $vetProfessionals->addFieldToFilter('vet_service_center', '1');
        }

        $now = new \DateTime('now', new \DateTimeZone('Asia/Kolkata'));
        $writer = new \Laminas\Log\Writer\Stream(BP . '/var/log/vetListBySchedule-'.$now->format('d-m-Y').'.log');
        $logger = new \Laminas\Log\Logger();
        $logger->addWriter($writer);
        $logger->info(print_r("------------START-------------", true));
        $logger->info(print_r($sessionData, true));

        if (!empty($sessionData['detected_clinic'])) {
            $vetProfessionals->addFieldtoFilter('city', array('like' => $sessionData['detected_clinic']));
        }
        if (!empty($specialty)) {
            $vetProfessionals->addFieldtoFilter('select_specialisation', array('in' => $specialty));
        }
        if (!empty($sort)) {
            if ($sort == "overall_rating") {
                $vetProfessionals->setOrder($sort,'DESC');
            } elseif ($sort == "for_vet_consulting") {
                $vetProfessionals->setOrder($sort,'ASC');
            } elseif ($sort == "year_of_experience") {
                $vetProfessionals->setOrder($sort,'DESC');
            }
        }
        $logger->info(print_r($vetProfessionals->getSelect()->__toString(), true));
        $logger->info(print_r("-------------END--------------", true));
        return $vetProfessionals;
    }

    /*
    * Get filter specialty
    */
    public function getReqSpecialty()
    {
        $specialty = ($this->getRequest()->getParam('specialty')) ? $this->getRequest()->getParam('specialty') : '';
        return $specialty;
    }

    /*
    * Get filter sort
    */
    public function getReqSort()
    {
        $sort = ($this->getRequest()->getParam('sort')) ? $this->getRequest()->getParam('sort') : '';
        return $sort;
    }

    /*
    * Get professional image
    */
    public function getProfessionalimage($image)
    {
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        if (!empty($image)) {
            $imageurl = $mediaUrl."groomer/feature/".$image;
        } else {
            $imageurl = $mediaUrl.'catalog/product/placeholder/'.$this->getConfig('catalog/placeholder/thumbnail_placeholder');
        }
        return $imageurl;
    }

    /**
     * get professional specialisation
     */
    public function getSpecialisation()
    {
        $specialty = [
            'Veterinary Orthopaedics' => 'Veterinary Orthopaedics',
            'Veterinary Cardiology' => 'Veterinary Cardiology',
            'Veterinary Dermatology' => 'Veterinary Dermatology',
            'Veterinary Medicine' => 'Veterinary Medicine',
            'Veterinary Homeopathy' => 'Veterinary Homeopathy',
            'Avian and Exotics Medicine' => 'Avian and Exotics Medicine',
            'Veterinary Surgery and Radiology' => 'Veterinary Surgery and Radiology',
            'Veterinary Nutritionist' => 'Veterinary Nutritionist'
        ];
        return $specialty;
    }


    /**
     * Get professional review by id
     *
     * @return integer
     *
     */
    public function getProfessionalReviewCount($id)
    {
        $groomerReviewCollection = $this->groomerReviewCollectionFactory->create()->addFieldToFilter('is_active','1')->addFieldToFilter('groomer_id', ['eq' => $id])->setPageSize(10)->setOrder('groomerreview_id','DESC');
        return $groomerReviewCollection->count();
    }

    /**
     * Get professional city hub by id
     *
     * @return integer
     *
     */
    public function getProfessionalCityHub($id)
    {
        $cityHub = $this->hubFactory->create()->load($id);
        $city = $cityHub->getCity().', '.$cityHub->getState();
        return $city;
    }
}