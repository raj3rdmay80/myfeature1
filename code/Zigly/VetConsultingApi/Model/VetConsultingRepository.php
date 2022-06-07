<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_VetConsultingApi
 */
declare(strict_types=1);

namespace Zigly\VetConsultingApi\Model;

use Magento\Customer\Model\Customer;
use Zigly\Species\Model\BreedFactory;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Zigly\VetConsultingApi\Api\VetConsultingRepositoryInterface;
use Zigly\GroomingService\Model\ResourceModel\Grooming\CollectionFactory;

class VetConsultingRepository implements VetConsultingRepositoryInterface
{

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Collection
     */
    protected $groomingServiceFactory;

    /**
     * @var Customer
     */
    protected $customer;

    /**
     * @var BreedFactory
     */
    protected $breedFactory;

    /**
     * @var TimezoneInterface
    */
    protected $timezoneInterface;

    /**
     * Initialize service
     * @param Request $request
     * @param Customer $customer
     * @param BreedFactory $breedFactory
     * @param TimezoneInterface $timezoneInterface
     * @param CollectionFactory $groomingServiceFactory
     * @param VetConsultingSearchResultsInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        Request $request,
        Customer $customer,
        BreedFactory $breedFactory,
        TimezoneInterface $timezoneInterface,
        CollectionFactory $groomingServiceFactory
    ) {
        $this->request = $request;
        $this->customer = $customer;
        $this->breedFactory = $breedFactory;
        $this->timezoneInterface = $timezoneInterface;
        $this->groomingServiceFactory = $groomingServiceFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function vetConsultingListing(SearchCriteriaInterface $searchCriteria)
    {
        try{
            $data = $this->request->getBodyParams();
            $scheduleFormatDate = $this->getScheduledDate($data['scheduled_date']);
            if(empty($data['token'])){
                throw new NoSuchEntityException(__('Please enter valid token.'));
            }
            if($data['scheduled_date'] != $scheduleFormatDate || empty($data['scheduled_date'])) {
                throw new NoSuchEntityException(__('Please select year-month-day format.'));
            }
            $pageSize = $searchCriteria->getPageSize();
            $currentPage = $searchCriteria->getCurrentPage();
            $ciphering = "AES-128-CTR";
            $ivLength = openssl_cipher_iv_length($ciphering);
            $options = 0;
            $decryptionIv = '1234567891022222';
            $decryptionKey = "inzigly";
            $decryption = openssl_decrypt($data['token'], $ciphering, $decryptionKey, $options, $decryptionIv);
            if ($decryption) {
                $decryptData = explode(":", $decryption);
                if (count($decryptData) && !empty($decryptData[0]) && !empty($decryptData[1])) {
                    $now = new \DateTime('now', new \DateTimeZone('Asia/Kolkata'));
                    $currentTimeStamp = $now->getTimestamp() + $now->getOffset();
                    if($currentTimeStamp - (int)$decryptData[1] > 604800){
                        throw new NoSuchEntityException(__('Invalid token.'));
                    }
                    $vetServiceCollection = $this->groomingServiceFactory->create()
                                    ->addFieldToFilter('main_table.booking_type', 2)
                                    ->addFieldToFilter('main_table.scheduled_date', $data['scheduled_date'])
                                    ->addFieldToFilter('main_table.groomer_id', $decryptData[0])
                                    ->setPageSize($pageSize)->setCurPage($currentPage);
                    $vetServiceCollection->getSelect()->join(["grommer_details" => $vetServiceCollection->getTable("zigly_groomer_groomer")],'main_table.groomer_id = grommer_details.groomer_id')->where("grommer_details.api_token_created_at = ".$decryptData[1])->columns("*");
                }
            }
            if(!isset($vetServiceCollection) && empty($vetServiceCollection[0]['api_token_created_at'])) {
                throw new NoSuchEntityException(__('Invalid token.'));
            }
            if(!count($vetServiceCollection)) {
                throw new NoSuchEntityException(__('No Data Found.'));
            }
            $vetCollection = [];
            foreach($vetServiceCollection as $vetData){
                $customerName = $this->customer->load($vetData->getCustomerId());
                $breedData = $this->breedFactory->create()->load($vetData->getPetBreed());
                $experience = $vetData->getYearOfExperience();
                $experience = ($experience == 0 || $experience == NULL)? '0 Years': "$experience Years";
                $painPoints = empty($vetData->getPainPoints()) ? null : json_decode($vetData->getPainPoints());
                $vetCollection[] = [
                    "id" => $vetData->getId(),
                    "professional_name" => $vetData->getName(),
                    "customer_name" => $customerName->getName(),
                    "status" => $vetData->getBookingStatus(),
                    "scheduled_time" => $vetData->getScheduledTime(),
                    "scheduled_date" => $vetData->getScheduledDate(),
                    "center" => "In-Clinic Appointments",
                    "subtotal" => $vetData->getSubtotal(),
                    "street" => trim($vetData->getStreet()),
                    "city"  => trim($vetData->getCity()),
                    "region" => trim($vetData->getRegion()),
                    "postcode" => $vetData->getPostcode(),
                    "experience" => $experience,
                    "certification" => $vetData->getCertification(),
                    "duration" => "30 Mins",
                    "payment_status" => $vetData->getPaymentStatus(),
                    "pain_points" => $painPoints,
                    "pain_description" => $vetData->getPainDescription(),
                    "pet_name" => $vetData->getPetName(),
                    "pet_age" => $vetData->getPetAge(),
                    "pet_breed" => $breedData->getName()
                ];
            }
            $response = new \Magento\Framework\DataObject();
            $response->setSearchCriteria($searchCriteria);
            $response->setItems($vetCollection);
            $response->setCurrentPageCount(count($vetServiceCollection));
            $response->setTotalCount($vetServiceCollection->getSize());
            return $response;
        } catch (\Exception $e) {
            throw new NoSuchEntityException(__($e->getMessage()), $e);
        }
    }

    /**
     * {@inheritdoc}
    */
    public function vetConsultingById()
    {
        try{
            $data = $this->request->getBodyParams();
            if(empty($data['entity_id'])){
                throw new NoSuchEntityException(__('Please enter entity id.'));
            }
            if(empty($data['token'])){
                throw new NoSuchEntityException(__('Please enter valid token.'));
            }
            $ciphering = "AES-128-CTR";
            $ivLength = openssl_cipher_iv_length($ciphering);
            $options = 0;
            $decryptionIv = '1234567891022222';
            $decryptionKey = "inzigly";
            $decryption = openssl_decrypt($data['token'], $ciphering, $decryptionKey, $options, $decryptionIv);
            if ($decryption) {
                $decryptData = explode(":", $decryption);
                if (count($decryptData) && !empty($decryptData[0]) && !empty($decryptData[1])) {
                    $now = new \DateTime('now', new \DateTimeZone('Asia/Kolkata'));
                    $currentTimeStamp = $now->getTimestamp() + $now->getOffset();
                    if($currentTimeStamp - (int)$decryptData[1] > 604800){
                        throw new NoSuchEntityException(__('Invalid token.'));
                    }
                    $vetConsultCollection = $this->groomingServiceFactory->create()
                                    ->addFieldToFilter('main_table.booking_type', 2)
                                    ->addFieldToFilter('main_table.entity_id', $data['entity_id'])
                                    ->addFieldToFilter('main_table.groomer_id', $decryptData[0]);
                    $vetConsultCollection->getSelect()->join(["grommer_details" => $vetConsultCollection->getTable("zigly_groomer_groomer")],'main_table.groomer_id = grommer_details.groomer_id')->where("grommer_details.api_token_created_at = ".$decryptData[1])->columns("*");
                }
            }
            if(!isset($vetConsultCollection) && empty($vetConsultCollection['api_token_created_at'])) {
                throw new NoSuchEntityException(__('Invalid token.'));
            }
            if(!count($vetConsultCollection)) {
                throw new NoSuchEntityException(__('No Data Found.'));
            }
            $vetItems = [];
            foreach($vetConsultCollection as $vetData){
                $customerName = $this->customer->load($vetData->getCustomerId());
                $breedData = $this->breedFactory->create()->load($vetData->getPetBreed());
                $experience = $vetData->getYearOfExperience();
                $experience = ($experience == 0 || $experience == NULL)? '0 Years': "$experience Years";
                $painPoints = empty($vetData->getPainPoints()) ? null : json_decode($vetData->getPainPoints());
                $vetItems[] = [
                    "id" => $vetData->getId(),
                    "professional_name" => $vetData->getName(),
                    "customer_name" => $customerName->getName(),
                    "status" => $vetData->getBookingStatus(),
                    "scheduled_time" => $vetData->getScheduledTime(),
                    "scheduled_date" => $vetData->getScheduledDate(),
                    "center" => "In-Clinic Appointments",
                    "subtotal" => $vetData->getSubtotal(),
                    "street" => trim($vetData->getStreet()),
                    "city"  => trim($vetData->getCity()),
                    "region" => trim($vetData->getRegion()),
                    "postcode" => $vetData->getPostcode(),
                    "experience" => $experience,
                    "certification" => $vetData->getCertification(),
                    "duration" => "30 Mins",
                    "payment_status" => $vetData->getPaymentStatus(),
                    "pain_points" => $painPoints,
                    "pain_description" => $vetData->getPainDescription(),
                    "pet_name" => $vetData->getPetName(),
                    "pet_age" => $vetData->getPetAge(),
                    "pet_breed" => $breedData->getName()
                ];
            }
            $response = new \Magento\Framework\DataObject();
            $response->setItems($vetItems);
            return $response;
        } catch (\Exception $e) {
            throw new NoSuchEntityException(__($e->getMessage()), $e);
        }
    }

    public function getScheduledDate($date)
    {
        return $this->timezoneInterface->date(new \DateTime($date))->format('Y-m-d');
    }
}