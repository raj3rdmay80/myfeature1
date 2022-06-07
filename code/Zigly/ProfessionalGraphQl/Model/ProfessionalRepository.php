<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_ProfessionalGraphQl
 */
declare(strict_types=1);

namespace Zigly\ProfessionalGraphQl\Model;

use Magento\Framework\App\Area;
use Magento\Store\Model\StoreManager;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Zigly\Groomer\Model\GroomerFactory;
use Zigly\ProfessionalGraphQl\Model\ImageUploader;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Zigly\ProfessionalGraphQl\Helper\Encryption;
use Zigly\ProfessionalGraphQl\Api\ProfessionalRepositoryInterface;
use Zigly\Groomer\Model\ResourceModel\Groomer\CollectionFactory as GroomerCollectionFactory;

class ProfessionalRepository implements ProfessionalRepositoryInterface
{

    /**
    * MSG91 authkey config path
    */
    const XML_PATH_MSG_AUTHKEY = 'msggateway/general/authkey';

    /**
    * @var ScopeConfigInterface
    */
    protected $scopeConfig;

    /** @var Encryption */
    protected $encryption;

    /**
     * @var GroomerFactory
     */
    protected $groomerFactory;

    /**
     * @var GroomerCollectionFactory
     */
    protected $groomerCollectionFactory;

    /**
     * @param StoreManager $storeManager
     * @param ImageUploader $imageUploader
     * @param Encryption $encryption
     * @param GroomerFactory $groomerFactory
     * @param GroomerCollectionFactory $groomerCollectionFactory
     */
    public function __construct(
        StoreManager $storeManager,
        StateInterface $inlineTranslation,
        TransportBuilder $transportBuilder,
        ScopeConfigInterface $scopeConfig,
        ImageUploader $imageUploader,
        Encryption $encryption,
        GroomerFactory $groomerFactory,
        GroomerCollectionFactory $groomerCollectionFactory
    ) {
        $this->storeManager = $storeManager;
        $this->imageUploader = $imageUploader;
        $this->scopeConfig = $scopeConfig;
        $this->encryption = $encryption;
        $this->groomerFactory = $groomerFactory;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->groomerCollectionFactory = $groomerCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function update($professional, $token)
    {
        if (!isset($professional['phone_number']) || empty($professional['phone_number']) || !is_numeric($professional['phone_number']) || strlen($professional['phone_number']) > 10 || strlen($professional['phone_number']) < 10)
        {
            throw new CouldNotSaveException(__('Please enter valid phone number.'));
        }
        if (!isset($professional['email']) || empty($professional['email']))
        {
            throw new CouldNotSaveException(__('Please enter email.'));
        }
        if (empty($token)) {
            throw new CouldNotSaveException(__('Please enter professional id.'));
        }
        if (isset($professional['select_specialisation']) && !empty($professional['select_specialisation']) && $professional['select_specialisation'] == "Other") {
            if (!isset($professional['specialisation_other']) || empty($professional['specialisation_other']))
            {
                throw new CouldNotSaveException(__('Please enter other for specialisation.'));
            }
        } else {
            $professional['specialisation_other'] = '';
        }
        $professionalUpdate = $this->encryption->tokenAuthentication($token);
        if (!$professionalUpdate) {
            throw new NoSuchEntityException(__('Invalid token'));
        }
        $professionalCollection = $this->groomerCollectionFactory->create()->addFieldToFilter('groomer_id', array('neq' => $professionalUpdate->getGroomerId()));
        foreach ($professionalCollection as $value) {
            if ($professional['phone_number'] && $value['phone_number'] == $professional['phone_number']) {
                throw new CouldNotSaveException(__('Phone number already exists.'));
            }
            if ($professional['email'] && $value['email'] == $professional['email']) {
                throw new CouldNotSaveException(__('Email Id already exists.'));
            }
        }
        /*profile image*/
        if (isset($_FILES['profile_image']['name']) && isset($_FILES['profile_image']['tmp_name']) && !empty($_FILES['profile_image']['name']) && !empty($_FILES['profile_image']['tmp_name'])) {
            if (($_FILES['profile_image']['type'] != "image/jpeg") && ($_FILES['profile_image']['type'] != "image/png") && ($_FILES['profile_image']['type'] != "image/jpg") )
            {
                throw new CouldNotSaveException(__('Only .jpg, .jpeg, .png extensions are allowed'));
            }
            $image = $this->imageUploader->saveFileToTmpDir('profile_image');
            $professionalUpdate->setProfileImage($image['file']);

        }
        /*aadhar image*/
        if (isset($_FILES['aadhar_img']['name']) && isset($_FILES['aadhar_img']['tmp_name']) && !empty($_FILES['aadhar_img']['name']) && !empty($_FILES['aadhar_img']['tmp_name'])) {
            if (($_FILES['aadhar_img']['type'] != "image/jpeg") && ($_FILES['aadhar_img']['type'] != "image/png") && ($_FILES['aadhar_img']['type'] != "image/jpg") )
            {
                throw new CouldNotSaveException(__('Only .jpg, .jpeg, .png extensions are allowed'));
            }
            $image = $this->imageUploader->saveFileToTmpDir('aadhar_img');
            $professionalUpdate->setAadharImg($image['file']);
        }
        /*aadhar back image*/
        if (isset($_FILES['aadhar_img_back']['name']) && isset($_FILES['aadhar_img_back']['tmp_name']) && !empty($_FILES['aadhar_img_back']['name']) && !empty($_FILES['aadhar_img_back']['tmp_name'])) {
            if (($_FILES['aadhar_img_back']['type'] != "image/jpeg") && ($_FILES['aadhar_img_back']['type'] != "image/png") && ($_FILES['aadhar_img_back']['type'] != "image/jpg") )
            {
                throw new CouldNotSaveException(__('Only .jpg, .jpeg, .png extensions are allowed'));
            }
            $image = $this->imageUploader->saveFileToTmpDir('aadhar_img_back');
            $professionalUpdate->setAadharImgBack($image['file']);
        }
        /*pan image*/
        if (isset($_FILES['pan_img']['name']) && isset($_FILES['pan_img']['tmp_name']) && !empty($_FILES['pan_img']['name']) && !empty($_FILES['pan_img']['tmp_name'])) {
            if (($_FILES['pan_img']['type'] != "image/jpeg") && ($_FILES['pan_img']['type'] != "image/png") && ($_FILES['pan_img']['type'] != "image/jpg") )
            {
                throw new CouldNotSaveException(__('Only .jpg, .jpeg, .png extensions are allowed'));
            }
            $image = $this->imageUploader->saveFileToTmpDir('pan_img');
            $professionalUpdate->setPanImg($image['file']);
        }
        /*upload certificate*/
        if (isset($_FILES['upload_certificate']['name']) && isset($_FILES['upload_certificate']['tmp_name']) && !empty($_FILES['upload_certificate']['name']) && !empty($_FILES['upload_certificate']['tmp_name'])) {
            if (($_FILES['upload_certificate']['type'] != "image/jpeg") && ($_FILES['upload_certificate']['type'] != "image/png") && ($_FILES['upload_certificate']['type'] != "image/jpg") )
            {
                throw new CouldNotSaveException(__('Only .jpg, .jpeg, .png extensions are allowed'));
            }
            $image = $this->imageUploader->saveFileToTmpDir('upload_certificate');
            $professionalUpdate->setUploadCertificate($image['file']);
        }
        /*cheque image*/
        if (isset($_FILES['cheque_image']['name']) && isset($_FILES['cheque_image']['tmp_name']) && !empty($_FILES['cheque_image']['name']) && !empty($_FILES['cheque_image']['tmp_name'])) {
            if (($_FILES['cheque_image']['type'] != "image/jpeg") && ($_FILES['cheque_image']['type'] != "image/png") && ($_FILES['cheque_image']['type'] != "image/jpg") )
            {
                throw new CouldNotSaveException(__('Only .jpg, .jpeg, .png extensions are allowed'));
            }
            $image = $this->imageUploader->saveFileToTmpDir('cheque_image');
            $professionalUpdate->setChequeImage($image['file']);
        }
        if (isset($professional['same_as_permanent_address']) && $professional['same_as_permanent_address'] == 1){
            $professionalUpdate->setCurrentHouseNo($professional['permanent_house_no']);
            $professionalUpdate->setCurrentLocality($professional['permanent_locality']);
            $professionalUpdate->setCurrentCity($professional['permanent_city']);
            $professionalUpdate->setCurrentPincode($professional['permanent_pincode']);
            $professionalUpdate->setCurrentState($professional['permanent_state']);
        } else {
            $professionalUpdate->setCurrentHouseNo($professional['current_house_no']);
            $professionalUpdate->setCurrentLocality($professional['current_locality']);
            $professionalUpdate->setCurrentCity($professional['current_city']);
            $professionalUpdate->setCurrentPincode($professional['current_pincode']);
            $professionalUpdate->setCurrentState($professional['current_state']);
        }
        $professionalUpdate->setName($professional['name']);
        $professionalUpdate->setEmail($professional['email']);
        $professionalUpdate->setPhoneNumber($professional['phone_number']);
        $professionalUpdate->setProfessionalRole($professional['professional_role']);
        $professionalUpdate->setDescription($professional['description']);
        $professionalUpdate->setCertification($professional['certification']);
        $professionalUpdate->setOnboardDate($professional['onboard_date']);
        $professionalUpdate->setCityHub($professional['city_hub']);
        $professionalUpdate->setFatherName($professional['father_name']);
        $professionalUpdate->setDob($professional['dob']);
        $professionalUpdate->setPermanentHouseNo($professional['permanent_house_no']);
        $professionalUpdate->setPermanentLocality($professional['permanent_locality']);
        $professionalUpdate->setPermanentCity($professional['permanent_city']);
        $professionalUpdate->setPermanentPincode($professional['permanent_pincode']);
        $professionalUpdate->setPermanentState($professional['permanent_state']);
        $professionalUpdate->setGender($professional['gender']);
        $professionalUpdate->setAccountDetails($professional['account_no']);
        $professionalUpdate->setAccountHolderName($professional['account_holder_name']);
        $professionalUpdate->setIfscCode($professional['ifsc_code']);
        $professionalUpdate->setBank($professional['bank']);
        $professionalUpdate->setCompanyName($professional['company_name']);
        $professionalUpdate->setGstin($professional['gstin']);
        $professionalUpdate->setTotalRating($professional['total_rating']);
        $professionalUpdate->setOverallRating($professional['overall_rating']);
        $professionalUpdate->setCreditBalance($professional['credit_balance']);
        $professionalUpdate->setCompletedJob($professional['completed_job']);
        $professionalUpdate->setRejectedJob($professional['rejected_job']);
        $professionalUpdate->setCancelledJob($professional['cancelled_job']);
        $professionalUpdate->setValueProductPurchased($professional['value_product_purchased']);
        $professionalUpdate->setUniversityCollege($professional['university_college']);
        $professionalUpdate->setYearOfPassing($professional['year_of_passing']);
        $professionalUpdate->setRegistrationCouncil($professional['registration_council']);
        $professionalUpdate->setRegistrationNumber($professional['registration_number']);
        $professionalUpdate->setYearOfRegistration($professional['year_of_registration']);
        $professionalUpdate->setYearOfExperience($professional['year_of_experience']);
        $professionalUpdate->setDoctorMembership($professional['doctor_membership']);
        $professionalUpdate->setAwards($professional['awards']);
        $professionalUpdate->setSelectSpecialisation($professional['select_specialisation']);
        $professionalUpdate->setSpecialisationOther($professional['specialisation_other']);
        $professionalUpdate->setClinicName($professional['clinic_name']);
        $professionalUpdate->setGstNumber($professional['gst_number']);
        $professionalUpdate->setAddressLine($professional['address_line']);
        $professionalUpdate->setLocality($professional['locality']);
        $professionalUpdate->setCity($professional['city']);
        $professionalUpdate->setPincode($professional['pincode']);
        $professionalUpdate->setState($professional['state']);
        $professionalUpdate->setSelectFacilities($professional['select_facilities']);
        $professionalUpdate->save();
        $professionalUpdate->setProfessionalId($professionalUpdate->getGroomerId());
        if (!empty($professionalUpdate->getEmail()) && $professionalUpdate->getStatus() == 0) {
            $templateVariable['email'] = $professionalUpdate->getEmail();
            $templateVariable['template_id'] = 'status_notify/status/professional_detail_email';
            $this->sendMail($templateVariable);
        }
        if (!empty($professionalUpdate->getPhoneNumber()) && $professionalUpdate->getStatus() == 0) {
            $serviceVar['mobileNo'] = $professionalUpdate->getPhoneNumber();
            $serviceVar['templateid'] = 'status_notify/status/professional_detail_template_id';
            $this->encryption->sendProfessionalSms($serviceVar);
        }
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA )."groomer/feature/";
        if ($professionalUpdate->getProfileImage()) {
            $profileImage = $mediaUrl.$professionalUpdate->getProfileImage();
            $professionalUpdate->setProfileImage($profileImage);
        }
        if ($professionalUpdate->getAadharImg()) {
            $aadharImage = $mediaUrl.$professionalUpdate->getAadharImg();
            $professionalUpdate->setAadharImg($aadharImage);
        }
        if ($professionalUpdate->getAadharImgBack()) {
            $aadharBackImage = $mediaUrl.$professionalUpdate->getAadharImgBack();
            $professionalUpdate->setAadharImgBack($aadharBackImage);
        }
        if ($professionalUpdate->getPanImg()) {
            $panImage = $mediaUrl.$professionalUpdate->getPanImg();
            $professionalUpdate->setPanImg($panImage);
        }
        if ($professionalUpdate->getUploadCertificate()) {
            $certificate = $mediaUrl.$professionalUpdate->getUploadCertificate();
            $professionalUpdate->setUploadCertificate($certificate);
        }
        if ($professionalUpdate->getChequeImage()) {
            $cheque = $mediaUrl.$professionalUpdate->getChequeImage();
            $professionalUpdate->setChequeImage($cheque);
        }
        return $professionalUpdate->getDataModel();
    }

    /*
    * send email function
    */
    public function sendMail($templatevariable)
    {
        $this->inlineTranslation->suspend();
        try {
            $storeId = $this->storeManager->getStore()->getId();
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $email = $this->scopeConfig->getValue('trans_email/ident_sales/email', $storeScope, $storeId);
            $name = $this->scopeConfig->getValue('trans_email/ident_sales/name', $storeScope, $storeId);
            $templateid = $this->scopeConfig->getValue(''.$templatevariable['template_id'].'', $storeScope, $storeId);
            $sender = [
                'name' => $name,
                'email' => $email,
            ];

            $transport = $this->transportBuilder->setTemplateIdentifier(
                    $templateid
                )->setTemplateOptions(
                    [
                        'area' => Area::AREA_FRONTEND,
                        'store' => $storeId
                    ]
                )->setTemplateVars(
                    ['cancelDetails' => $templatevariable]
                )->setFrom(
                    $sender
                )->addTo(
                    $templatevariable['email']
                )->getTransport();
            $transport->sendMessage();
        } catch (\Exception $e) {
            //$this->logger->debug($e->getMessage());
        }
        $this->inlineTranslation->resume();
        return $this;
    }
}