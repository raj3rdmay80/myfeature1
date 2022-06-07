<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_ProfessionalGraphQl
 */
declare(strict_types=1);

namespace Zigly\ProfessionalGraphQl\Model;

use Zigly\Login\Helper\Smsdata;
use Magento\Store\Model\StoreManager;
use Zigly\ProfessionalGraphQl\Helper\Encryption;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\AuthenticationException;
use Zigly\ProfessionalGraphQl\Model\ProfessionalFactory;
use Zigly\Groomer\Model\ResourceModel\Groomer\CollectionFactory;
use Zigly\ProfessionalGraphQl\Api\ProfessionalLoginRepositoryInterface;

class ProfessionalLoginRepository implements ProfessionalLoginRepositoryInterface
{

    /**
     * @var ProfessionalFactory
     */
    protected $professionalFactory;

    /**
     * @var Smsdata
     */
    private $helperData;

    /** @var Encryption */
    protected $encryption;

    /**
     * @param Smsdata $helperData
     * @param Encryption $encryption
     * @param StoreManager $storeManager
     * @param CollectionFactory $collectionFactory
     * @param ProfessionalFactory $professionalFactory
     */
    public function __construct(
        Smsdata $helperData,
        Encryption $encryption,
        StoreManager $storeManager,
        CollectionFactory $collectionFactory,
        ProfessionalFactory $professionalFactory
    ) {
        $this->encryption = $encryption;
        $this->helperData = $helperData;
        $this->storeManager = $storeManager;
        $this->collectionFactory = $collectionFactory;
        $this->professionalFactory = $professionalFactory;
    }

    /**
     * Send Login Otp
     * @param string $phone_number
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sendOtp($phone_number)
    {
        if (!isset($phone_number) || empty($phone_number) || !is_numeric($phone_number) || strlen($phone_number) > 10 || strlen($phone_number) < 10)
        {
            throw new NoSuchEntityException(__('Please enter valid phone number.'));
        }
        try {
            $professionalData = [];
            $templateId = 'msggateway/otpemail/login_professional_otp';
            $loginOtp = $this->helperData->sendloginotp($phone_number, $templateId);
            $professionalData['status'] = ($loginOtp['status'] == 1) ? true : false;
            $professionalModel = $this->professionalFactory->create()->setData($professionalData);
            return $professionalModel->getDataModel();
        } catch (AuthenticationException $e) {
            throw new NoSuchEntityException(__($e->getMessage()), $e);
        }
    }

    /**
     * resend Login Otp
     * @param string $phone_number
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function resendOtp($phone_number)
    {
        if (!isset($phone_number) || empty($phone_number) || !is_numeric($phone_number) || strlen($phone_number) > 10 || strlen($phone_number) < 10)
        {
            throw new NoSuchEntityException(__('Please enter valid phone number.'));
        }
        try {
            $professionalData = [];
            $resendOtp = $this->helperData->resendloginotp($phone_number);
            $professionalData['status'] = ($resendOtp['status'] == 1) ? true : false;
            $professionalModel = $this->professionalFactory->create()->setData($professionalData);
            return $professionalModel->getDataModel();
        } catch (AuthenticationException $e) {
            throw new NoSuchEntityException(__($e->getMessage()), $e);
        }
    }

    /**
     * Generate Login token
     * @param string $phone_number
     * @param string $otp
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function generateToken($phone_number, $otp)
    {
        if (!isset($phone_number) || empty($phone_number) || !is_numeric($phone_number) || strlen($phone_number) > 10 || strlen($phone_number) < 10)
        {
            throw new NoSuchEntityException(__('Please enter valid phone number.'));
        }
        if (!isset($otp)|| empty($otp) || !is_numeric($otp) || strlen($otp) > 4 || strlen($otp) < 4)
        {
            throw new GraphQlInputException(__('Please enter valid OTP.'));
        }
        try {
            $professionalData = [];
            $professionalData['is_new'] = false;
            $professionalData['professional_status'] = false;
            $verifyOtp = $this->helperData->verifyotp($phone_number, $otp);
            if ($verifyOtp['status'] == 1) {
                $professionalCollection = $this->collectionFactory->create();
                $professional = $professionalCollection->getItemByColumnValue('phone_number', $phone_number);
                if (!$professional) {
                    $professional = $professionalCollection->getNewEmptyItem();
                    $professional->setPhoneNumber($phone_number)->save();
                } 
                $encryption = '';
                $now = new \DateTime('now', new \DateTimeZone('Asia/Kolkata'));
                $currentTimeStamp = $now->getTimestamp() + $now->getOffset();
                $id = $professional->getId();
                $ciphering = "AES-128-CTR";
                $iv_length = \openssl_cipher_iv_length($ciphering);
                $options = 0;
                $encryption_iv = '1234567891022222';
                $encryption_key = "inzigly";
                $encryption = \openssl_encrypt($id.':'.$currentTimeStamp, $ciphering,
                $encryption_key, $options, $encryption_iv);
                $professional->setApiToken($encryption)->setApiTokenCreatedAt($currentTimeStamp)->save();
                if (empty($professional->getEmail())) {
                    $professionalData['is_new'] = true;
                }
                $professionalData['status'] = true;
                $professionalData['token'] = $encryption;
                $professionalData['professional_status'] = $professional->getStatus();
            } else {
                $professionalData['status'] = false;
                $professionalData['token'] = false;
            }
            $professionalModel = $this->professionalFactory->create()->setData($professionalData);
            return $professionalModel->getDataModel();
        } catch (AuthenticationException $e) {
            throw new NoSuchEntityException(__($e->getMessage()), $e);
        }
    }

    /**
     * Professional logout
     * @param string $token
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function logout($token)
    {
        try {
            $professionalData = [];
            $professional = $this->encryption->tokenAuthentication($token);
            if (!$professional) {
                throw new NoSuchEntityException(__('Invalid token'));
            }
            $professional->setApiToken(Null)->save();
            $professionalData['status'] = true;
            $professionalModel = $this->professionalFactory->create()->setData($professionalData);
            return $professionalModel->getDataModel();
        } catch (AuthenticationException $e) {
            throw new NoSuchEntityException(__($e->getMessage()), $e);
        }
    }

    /**
     * Professional listing
     * @param string $token
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function list($token)
    {
        try {
            $professionalData = [];
            $professional = $this->encryption->tokenAuthentication($token);
            if (!$professional) {
                throw new NoSuchEntityException(__('Invalid token'));
            }
            $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
            $profileImage = '';
            $aadharImage = '';
            $aadharBackImage = '';
            $panImage = '';
            $certificate = '';
            $cheque = '';
            $bdImage = '';
            if ($professional->getProfileImage()) {
                $profileImage = $mediaUrl."groomer/feature/".$professional->getProfileImage();
            }
            if ($professional->getAadharImg()) {
                $aadharImage = $mediaUrl."groomer/feature/".$professional->getAadharImg();
            }
            if ($professional->getAadharImgBack()) {
                $aadharBackImage = $mediaUrl."groomer/feature/".$professional->getAadharImgBack();
            }
            if ($professional->getPanImg()) {
                $panImage = $mediaUrl."groomer/feature/".$professional->getPanImg();
            }
            if ($professional->getUploadCertificate()) {
                $certificate = $mediaUrl."groomer/feature/".$professional->getUploadCertificate();
            }
            if ($professional->getChequeImage()) {
                $cheque = $mediaUrl."groomer/feature/".$professional->getChequeImage();
            }
            if ($professional->getBdProfileImage()) {
                $bdImage = $mediaUrl."groomer/feature/".$professional->getBdProfileImage();
            }
            $professionalStatus = ["0"=>"Under Review","1"=>"Approved", "2"=>"On-Hold", "3"=>"Rejected"];
            if (array_key_exists($professional->getStatus(), $professionalStatus)) {
                $status = $professionalStatus[$professional->getStatus()];
            } else {
                $status = "";
            }
            $professionalRole = ["1"=>"Trainer","2"=>"Groomer", "3"=>"Vet", "4"=>"Behaviorist"];
            if (array_key_exists($professional->getProfessionalRole(), $professionalRole)) {
                $role = $professionalRole[$professional->getProfessionalRole()];
            } else {
                $role = "";
            }
            $professionalData['professional_id'] = $professional->getGroomerId();
            $professionalData['profile_image'] = $profileImage;
            $professionalData['name'] = ($professional->getName()) ? $professional->getName() : "";
            $professionalData['email'] = ($professional->getEmail()) ? $professional->getEmail() : "";
            $professionalData['status'] = $status;
            $professionalData['phone_number'] = ($professional->getPhoneNumber()) ? $professional->getPhoneNumber() : "";
            $professionalData['description'] = ($professional->getDescription()) ? $professional->getDescription() : "";
            $professionalData['certification'] = ($professional->getCertification()) ? $professional->getCertification() : "";
            $professionalData['professional_role'] = $role;
            $professionalData['onboard_date'] = ($professional->getOnboardDate()) ? $professional->getOnboardDate() : "";
            $professionalData['city_hub'] = ($professional->getCityHub()) ? $professional->getCityHub() : "";
            $professionalData['father_name'] = ($professional->getFatherName()) ? $professional->getFatherName() : "";
            $professionalData['dob'] = ($professional->getDob()) ? $professional->getDob() : "";
            $professionalData['gender'] = ($professional->getGender()) ? $professional->getGender() : "";
            $professionalData['permanent_house_no'] = ($professional->getPermanentHouseNo()) ? $professional->getPermanentHouseNo() : "";
            $professionalData['permanent_locality'] = ($professional->getPermanentLocality()) ? $professional->getPermanentLocality() : "";
            $professionalData['permanent_city'] = ($professional->getPermanentCity()) ? $professional->getPermanentCity() : "";
            $professionalData['permanent_pincode'] = ($professional->getPermanentPincode()) ? $professional->getPermanentPincode() : "";
            $professionalData['permanent_state'] = ($professional->getPermanentState()) ? $professional->getPermanentState() : "";
            $professionalData['aadhar_img'] = $aadharImage;
            $professionalData['aadhar_img_back'] = $aadharBackImage;
            $professionalData['pan_img'] = $panImage;
            $professionalData['upload_certificate'] = $certificate;
            $professionalData['cheque_image'] = $cheque;
            $professionalData['current_house_no'] = ($professional->getCurrentHouseNo()) ? $professional->getCurrentHouseNo() : "";
            $professionalData['current_locality'] = ($professional->getCurrentLocality()) ? $professional->getCurrentLocality() : "";
            $professionalData['current_city'] = ($professional->getCurrentCity()) ? $professional->getCurrentCity() : "";
            $professionalData['current_pincode'] = ($professional->getCurrentPincode()) ? $professional->getCurrentPincode() : "";
            $professionalData['current_state'] = ($professional->getCurrentState()) ? $professional->getCurrentState() : "";
            $professionalData['account_details'] = ($professional->getAccountDetails()) ? $professional->getAccountDetails() : "";
            $professionalData['account_holder_name'] = ($professional->getAccountHolderName()) ? $professional->getAccountHolderName() : "";
            $professionalData['ifsc_code'] = ($professional->getIfscCode()) ? $professional->getIfscCode() : "";
            $professionalData['bank'] = ($professional->getBank()) ? $professional->getBank() : "";
            $professionalData['company_name'] = ($professional->getCompanyName()) ? $professional->getCompanyName() : "";
            $professionalData['gstin'] = ($professional->getGstin()) ? $professional->getGstin() : "";
            $professionalData['total_rating'] = ($professional->getTotalRating()) ? $professional->getTotalRating() : "";
            $professionalData['overall_rating'] = ($professional->getOverallRating()) ? $professional->getOverallRating() : "";
            $professionalData['credit_balance'] = ($professional->getCreditBalance()) ? $professional->getCreditBalance() : "";
            $professionalData['completed_job'] = ($professional->getCompletedJob()) ? $professional->getCompletedJob() : "";
            $professionalData['rejected_job'] = ($professional->getRejectedJob()) ? $professional->getRejectedJob() : "";
            $professionalData['cancelled_job'] = ($professional->getCancelledJob()) ? $professional->getCancelledJob() : "";
            $professionalData['value_product_purchased'] = ($professional->getValueProductPurchased()) ? $professional->getValueProductPurchased() : "";
            $professionalData['university_college'] = ($professional->getUniversityCollege()) ? $professional->getUniversityCollege() : "";
            $professionalData['year_of_passing'] = ($professional->getYearOfPassing()) ? $professional->getYearOfPassing() : "";
            $professionalData['registration_council'] = ($professional->getRegistrationCouncil()) ? $professional->getRegistrationCouncil() : "";
            $professionalData['registration_number'] = ($professional->getRegistrationNumber()) ? $professional->getRegistrationNumber() : "";
            $professionalData['year_of_registration'] = ($professional->getYearOfRegistration()) ? $professional->getYearOfRegistration() : "";
            $professionalData['year_of_experience'] = ($professional->getYearOfExperience()) ? $professional->getYearOfExperience() : "";
            $professionalData['doctor_membership'] = ($professional->getDoctorMembership()) ? $professional->getDoctorMembership() : "";
            $professionalData['awards'] = ($professional->getAwards()) ? $professional->getAwards() : "";
            $professionalData['select_specialisation'] = ($professional->getSelectSpecialisation()) ? $professional->getSelectSpecialisation() : "";
            $professionalData['specialisation_other'] = ($professional->getSpecialisationOther()) ? $professional->getSpecialisationOther() : "";
            $professionalData['clinic_name'] = ($professional->getClinicName()) ? $professional->getClinicName() : "";
            $professionalData['gst_number'] = ($professional->getGstNumber()) ? $professional->getGstNumber() : "";
            $professionalData['address_line'] = ($professional->getAddressLine()) ? $professional->getAddressLine() : "";
            $professionalData['locality'] = ($professional->getLocality()) ? $professional->getLocality() : "";
            $professionalData['city'] = ($professional->getCity()) ? $professional->getCity() : "";
            $professionalData['pincode'] = ($professional->getPincode()) ? $professional->getPincode() : "";
            $professionalData['state'] = ($professional->getState()) ? $professional->getState() : "";
            $professionalData['select_facilities'] = ($professional->getSelectFacilities()) ? $professional->getSelectFacilities() : "";
            $professionalData['bd_name'] = ($professional->getBdName()) ? $professional->getBdName() : "";
            $professionalData['bd_email'] = ($professional->getBdEmail()) ? $professional->getBdEmail() : "";
            $professionalData['bd_phone_number'] = ($professional->getBdPhoneNumber()) ? $professional->getBdPhoneNumber() : "";
            $professionalData['bd_profile_image'] = $bdImage;
            $professionalModel = $this->professionalFactory->create()->setData($professionalData);
            $professionalModel->setProfileImage($profileImage);
            $professionalModel->setAadharImg($aadharImage);
            $professionalModel->setAadharImgBack($aadharBackImage);
            $professionalModel->setPanImg($panImage);
            $professionalModel->setUploadCertificate($certificate);
            $professionalModel->setChequeImage($cheque);
            $professionalModel->setBdProfileImage($bdImage);
            return $professionalModel->getDataModel();
        } catch (AuthenticationException $e) {
            throw new NoSuchEntityException(__($e->getMessage()), $e);
        }
    }
}