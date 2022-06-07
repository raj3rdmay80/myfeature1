<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_VetConsulting
 */
declare(strict_types=1);

namespace Zigly\VetConsulting\Block\Adminhtml;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\AuthorizationInterface;
use Zigly\GroomingService\Model\GroomingFactory;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;
use Zigly\Species\Model\BreedFactory;
use Zigly\Species\Model\SpeciesFactory;
use Zigly\Groomer\Model\GroomerFactory;
use Zigly\GroomingService\Helper\ServiceStatus;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

class Vet extends \Magento\Backend\Block\Template {

    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'view.phtml';

    /** @var CollectionFactory */
    protected $orderCollection;

    /**
     * @var $context
     */
    protected $context;

    /**
     * @param array $data
     * @param Context $context
     * @param AuthorizationInterface $authorization,
     * @param GroomingFactory $groomingFactory
     * @param CustomerFactory $customerFactory
     * @param GroupRepositoryInterface $groupRepository
     * @param PricingHelper $pricingHelper
     * @param BreedFactory $breedFactory
     * @param SpeciesFactory $speciesFactory
     * @param GroomerFactory $groomerFactory
     * @param CollectionFactory $orderCollection
     * @param ServiceStatus $serviceStatus
     */
    public function __construct(
        Context $context,
        AuthorizationInterface $authorization,
        GroomingFactory $groomingFactory,
        CustomerFactory $customerFactory,
        GroupRepositoryInterface $groupRepository,
        PricingHelper $pricingHelper,
        BreedFactory $breedFactory,
        SpeciesFactory $speciesFactory,
        GroomerFactory $groomerFactory,
        CollectionFactory $orderCollection,
        ServiceStatus $serviceStatus,
        array $data = []
    ) {
        $this->context = $context;
        $this->authorization = $authorization;
        $this->groomingFactory = $groomingFactory;
        $this->_customerFactory = $customerFactory;
        $this->groupRepository = $groupRepository;
        $this->pricingHelper = $pricingHelper;
        $this->breedFactory = $breedFactory;
        $this->speciesFactory = $speciesFactory;
        $this->groomerFactory = $groomerFactory;
        $this->orderCollection = $orderCollection;
        $this->serviceStatus = $serviceStatus;
        parent::__construct($context, $data);
    }

    /**
     * Return entity ID
     *
     * @return int|null
     */
    public function getEntityId()
    {
        return $this->context->getRequest()->getParam('entity_id');
    }

    /**
     * Return ACL check
     *
     * @return bol
     */
    public function getServiceStatusUpdateAcl()
    {
        return $this->authorization->isAllowed('Zigly_GroomingService::Grooming_status_update');
    }

    /*
     * get grooming service
     */
    public function getGroomingService()
    {
        $id = $this->getEntityId();
        $service = $this->groomingFactory->create()->load($id);
        return $service;
    }

    /**
     * Get object created at date
     *
     * @param string $createdAt
     * @return \DateTime
     */
    public function getServiceAdminDate($createdAt)
    {
        return $this->_localeDate->date(new \DateTime($createdAt));
    }

    /**
     * Get customer by id
     *
     * @param int $customerId
     */
    public function getCustomer($customerId)
    {
        return $this->_customerFactory->create()->load($customerId);
    }

    /**
     * Get breed detail
     *
     * @param int $id
     */
    public function getBreed($breed_id)
    {
        return $this->breedFactory->create()->load($breed_id);
    }

    /**
     * Get species detail
     *
     * @param int $id
     */
    public function getSpecies($species_id)
    {
        return $this->speciesFactory->create()->load($species_id);
    }

    /**
     * Get Service Type
     *
     * @param int $id
     * @return string
     */
    public function getServiceType($id)
    {
        $service_types = array(1 => 'Grooming', 2 => 'Vet Consultation', 3 => 'Behavior Consultation');
        return isset($service_types[$id]) ? $service_types[$id] : '';
    }

    /**
     * Get customer group name by id
     *
     * @param int $groupId
     * @return string
     */
    public function getCustomerGroupName($groupId){
        return $this->groupRepository->getById($groupId)->getCode();
    }

    /**
     * Return formated price
     *
     * @param int|float $price
     * @return  float|string
     */
    public function getFormattedPrice($price)
    {
        return $this->pricingHelper->currency($price, true, false);
    }

    /**
     * Return Service Status
     *
     * @return  array
     */
    public function getServiceStatus()
    {
        return $this->serviceStatus->getServiceStatus();
    }

    /**
     * Return groomer name
     *
     * @param int $groomer_id
     * @return  string
     */
    public function getGroomerName($groomer_id) {
        return $this->groomerFactory->create()->load($groomer_id)->getName();
    }

    /**
     * Return order increment id
     *
     * @param int $bookingId
     * @return []
     */
    public function getOrderDetails($bookingId) {
        // $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        // $order = $objectManager->create('Magento\Sales\Model\ResourceModel\Order\CollectionFactory')->create();
        $order = $this->orderCollection->create()->getItemByColumnValue('booking_id', $bookingId);
        if (!empty($order)) {
            return [
                'increment_id' => $order->getIncrementId(),
                'url' => $this->getUrl('sales/order/view', array('order_id' => $order->getEntityId()))
            ];
        }
        return [];
    }
}