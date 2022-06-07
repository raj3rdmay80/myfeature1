<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Groomer
 */
declare(strict_types=1);

namespace Zigly\Groomer\Api\Data;

interface GroomerInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const OVERALL_RATING = 'overall_rating';
    const PROFESSIONAL_ROLE = 'professional_role';
    const CANCELLED_JOB = 'cancelled_job';
    const ACCOUNT_DETAILS = 'account_details';
    const PROFILE_IMAGE = 'profile_image';
    const PERMANENT_LOCALITY = 'permanent_locality';
    const PERMANENT_STATE = 'permanent_state';
    const CURRENT_PINCODE = 'current_pincode';
    const ONBOARD_DATE = 'onboard_date';
    const EMAIL = 'email';
    const DOB = 'dob';
    const CURRENT_CITY = 'current_city';
    const BANK = 'bank';
    const PERMANENT_HOUSE_NO = 'permanent_house_no';
    const REJECTED_JOB = 'rejected_job';
    const COMPLETED_JOB = 'completed_job';
    const TOTAL_RATING = 'total_rating';
    const PERMANENT_CITY = 'permanent_city';
    const PHONE_NUMBER = 'phone_number';
    const ACCOUNT_HOLDER_NAME = 'account_holder_name';
    const PERMANENT_PINCODE = 'permanent_pincode';
    const IFSC_CODE = 'ifsc_code';
    const AADHAR_IMG = 'aadhar_img';
    const AADHAR_IMG_BACK = 'aadhar_img_back';
    const GROOMER_ID = 'groomer_id';
    const PROFESSIONAL_ID = 'professional_id';
    const PAN_IMG = 'pan_img';
    const CURRENT_STATE = 'current_state';
    const NAME = 'name';
    const GENDER = 'gender';
    const FATHER_NAME = 'father_name';
    const COMPANY_NAME = 'company_name';
    const CREDIT_BALANCE = 'credit_balance';
    const VALUE_PRODUCT_PURCHASED = 'value_product_purchased';
    const STATUS = 'status';
    const CITY_HUB = 'city_hub';
    const CURRENT_LOCALITY = 'current_locality';
    const CURRENT_HOUSE_NO = 'current_house_no';
    const GSTIN = 'gstin';
    const UNIVERSITY_COLLEGE = 'university_college';
    const YEAR_OF_PASSING = 'year_of_passing';
    const UPLOAD_CERTIFICATE = 'upload_certificate';
    const REGISTRATION_COUNCIL = 'registration_council';
    const REGISTRATION_NUMBER = 'registration_number';
    const YEAR_OF_REGISTRATION = 'year_of_registration';
    const YEAR_OF_EXPERIENCE = 'year_of_experience';
    const DOCTOR_MEMBERSHIP = 'doctor_membership';
    const AWARDS = 'awards';
    const SELECT_SPECIALISATION = 'select_specialisation';
    const CLINIC_NAME = 'clinic_name';
    const GST_NUMBER = 'gst_number';
    const ADDRESS_LINE = 'address_line';
    const LOCALITY = 'locality';
    const CITY = 'city';
    const PINCODE = 'pincode';
    const STATE = 'state';
    const SELECT_FACILITIES = 'select_facilities';
    const CHEQUE_IMAGE = 'cheque_image';

    /**
     * Get groomer_id
     * @return string|null
     */
    /*public function getGroomerId();*/

    /**
     * Set groomer_id
     * @param string $groomerId
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    /*public function setGroomerId($groomerId);*/

    /**
     * Get professional_id
     * @return string|null
     */
    public function getProfessionalId();

    /**
     * Set professional_id
     * @param string $professionalId
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setProfessionalId($professionalId);

    /**
     * Get profile_image
     * @return string|null
     */
    public function getProfileImage();

    /**
     * Set profile_image
     * @param string $profileImage
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setProfileImage($profileImage);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Zigly\Groomer\Api\Data\GroomerExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Zigly\Groomer\Api\Data\GroomerExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Zigly\Groomer\Api\Data\GroomerExtensionInterface $extensionAttributes
    );

    /**
     * Get name
     * @return string|null
     */
    public function getName();

    /**
     * Set name
     * @param string $name
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setName($name);

    /**
     * Get email
     * @return string|null
     */
    public function getEmail();

    /**
     * Set email
     * @param string $email
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setEmail($email);

    /**
     * Get phone_number
     * @return string|null
     */
    public function getPhoneNumber();

    /**
     * Set phone_number
     * @param string $phoneNumber
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setPhoneNumber($phoneNumber);

    /**
     * Get onboard_date
     * @return string|null
     */
    public function getOnboardDate();

    /**
     * Set onboard_date
     * @param string $onboardDate
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setOnboardDate($onboardDate);

    /**
     * Get city_hub
     * @return string|null
     */
    public function getCityHub();

    /**
     * Set city_hub
     * @param string $cityHub
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setCityHub($cityHub);

    /**
     * Get status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     * @param string $status
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setStatus($status);

    /**
     * Get professionalRole
     * @return string|null
     */
    public function getProfessionalRole();

    /**
     * Set professionalRole
     * @param string $professionalRole
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setProfessionalRole($professionalRole);

    /**
     * Get father_name
     * @return string|null
     */
    public function getFatherName();

    /**
     * Set father_name
     * @param string $fatherName
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setFatherName($fatherName);

    /**
     * Get dob
     * @return string|null
     */
    public function getDob();

    /**
     * Set dob
     * @param string $dob
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setDob($dob);

    /**
     * Get gender
     * @return string|null
     */
    public function getGender();

    /**
     * Set gender
     * @param string $gender
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setGender($gender);

    /**
     * Get permanent_house_no
     * @return string|null
     */
    public function getPermanentHouseNo();

    /**
     * Set permanent_house_no
     * @param string $permanentHouseNo
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setPermanentHouseNo($permanentHouseNo);

    /**
     * Get permanent_locality
     * @return string|null
     */
    public function getPermanentLocality();

    /**
     * Set permanent_locality
     * @param string $permanentLocality
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setPermanentLocality($permanentLocality);

    /**
     * Get permanent_city
     * @return string|null
     */
    public function getPermanentCity();

    /**
     * Set permanent_city
     * @param string $permanentCity
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setPermanentCity($permanentCity);

    /**
     * Get permanent_pincode
     * @return string|null
     */
    public function getPermanentPincode();

    /**
     * Set permanent_pincode
     * @param string $permanentPincode
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setPermanentPincode($permanentPincode);

    /**
     * Get permanent_state
     * @return string|null
     */
    public function getPermanentState();

    /**
     * Set permanent_state
     * @param string $permanentState
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setPermanentState($permanentState);

    /**
     * Get aadhar_img
     * @return string|null
     */
    public function getAadharImg();

    /**
     * Set aadhar_img
     * @param string $aadharImg
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setAadharImg($aadharImg);

    /**
     * Get aadhar_img_back
     * @return string|null
     */
    public function getAadharImgBack();

    /**
     * Set aadhar_img_back
     * @param string $aadharImgBack
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setAadharImgBack($aadharImgBack);

    /**
     * Get pan_img
     * @return string|null
     */
    public function getPanImg();

    /**
     * Set pan_img
     * @param string $panImg
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setPanImg($panImg);

    /**
     * Get current_house_no
     * @return string|null
     */
    public function getCurrentHouseNo();

    /**
     * Set current_house_no
     * @param string $currentHouseNo
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setCurrentHouseNo($currentHouseNo);

    /**
     * Get current_locality
     * @return string|null
     */
    public function getCurrentLocality();

    /**
     * Set current_locality
     * @param string $currentLocality
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setCurrentLocality($currentLocality);

    /**
     * Get current_city
     * @return string|null
     */
    public function getCurrentCity();

    /**
     * Set current_city
     * @param string $currentCity
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setCurrentCity($currentCity);

    /**
     * Get current_pincode
     * @return string|null
     */
    public function getCurrentPincode();

    /**
     * Set current_pincode
     * @param string $currentPincode
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setCurrentPincode($currentPincode);

    /**
     * Get current_state
     * @return string|null
     */
    public function getCurrentState();

    /**
     * Set current_state
     * @param string $currentState
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setCurrentState($currentState);

    /**
     * Get account_details
     * @return string|null
     */
    public function getAccountDetails();

    /**
     * Set account_details
     * @param string $accountDetails
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setAccountDetails($accountDetails);

    /**
     * Get account_holder_name
     * @return string|null
     */
    public function getAccountHolderName();

    /**
     * Set account_holder_name
     * @param string $accountHolderName
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setAccountHolderName($accountHolderName);

    /**
     * Get ifsc_code
     * @return string|null
     */
    public function getIfscCode();

    /**
     * Set ifsc_code
     * @param string $ifscCode
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setIfscCode($ifscCode);

    /**
     * Get bank
     * @return string|null
     */
    public function getBank();

    /**
     * Set bank
     * @param string $bank
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setBank($bank);

    /**
     * Get company_name
     * @return string|null
     */
    public function getCompanyName();

    /**
     * Set company_name
     * @param string $companyName
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setCompanyName($companyName);

    /**
     * Get gstin
     * @return string|null
     */
    public function getGstin();

    /**
     * Set gstin
     * @param string $gstin
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setGstin($gstin);

    /**
     * Get total_rating
     * @return string|null
     */
    public function getTotalRating();

    /**
     * Set total_rating
     * @param string $totalRating
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setTotalRating($totalRating);

    /**
     * Get overall_rating
     * @return string|null
     */
    public function getOverallRating();

    /**
     * Set overall_rating
     * @param string $overallRating
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setOverallRating($overallRating);

    /**
     * Get credit_balance
     * @return string|null
     */
    public function getCreditBalance();

    /**
     * Set credit_balance
     * @param string $creditBalance
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setCreditBalance($creditBalance);

    /**
     * Get completed_job
     * @return string|null
     */
    public function getCompletedJob();

    /**
     * Set completed_job
     * @param string $completedJob
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setCompletedJob($completedJob);

    /**
     * Get rejected_job
     * @return string|null
     */
    public function getRejectedJob();

    /**
     * Set rejected_job
     * @param string $rejectedJob
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setRejectedJob($rejectedJob);

    /**
     * Get cancelled_job
     * @return string|null
     */
    public function getCancelledJob();

    /**
     * Set cancelled_job
     * @param string $cancelledJob
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setCancelledJob($cancelledJob);

    /**
     * Get value_product_purchased
     * @return string|null
     */
    public function getValueProductPurchased();

    /**
     * Set value_product_purchased
     * @param string $valueProductPurchased
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setValueProductPurchased($valueProductPurchased);

    /**
     * Get university_name
     * @return string|null
     */
    public function getUniversityName();

    /**
     * Set university_name
     * @param string $UniversityName
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setUniversityName($universityName);

    /**
     * Get year of passing
     * @return string|null
     */
    public function getYearOfPassing();

    /**
     * Set year of passing
     * @param string $yearOfPassing
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setYearOfPassing($yearOfPassing);

    /**
     * Get registration council
     * @return string|null
     */
    public function getRegistrationCouncil();

    /**
     * Set registration council
     * @param string $registrationCouncil
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setRegistrationCouncil($registrationCouncil);

    /**
     * Get Registration Number
     * @return string|null
     */
    public function getRegistrationNumber();

    /**
     * Set Registration Number
     * @param string $registrationNumber
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setRegistrationNumber($registrationNumber);

    /**
     * Get YearOfRegistration
     * @return string|null
     */
    public function getYearOfRegistration();

    /**
     * Set YearOfRegistration
     * @param string $yearOfRegistration
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setYearOfRegistration($yearOfRegistration);

    /**
     * Get YearOfExperience
     * @return string|null
     */
    public function getYearOfExperience();

    /**
     * Set YearOfExperience
     * @param string $yearOfExperience
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setYearOfExperience($yearOfExperience);

    /**
     * Get DoctorMembership
     * @return string|null
     */
    public function getDoctorMembership();

    /**
     * Set DoctorMembership
     * @param string $doctorMembership
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setDoctorMembership($doctorMembership);

    /**
     * Get awards
     * @return string|null
     */
    public function getAwards();

    /**
     * Set awards
     * @param string $awards
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setAwards($awards);

    /**
     * Get SelectSpecialisation
     * @return string|null
     */
    public function getSelectSpecialisation();

    /**
     * Set SelectSpecialisation
     * @param string $selectSpecialisation
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setSelectSpecialisation($selectSpecialisation);

    /**
     * Get ClinicName
     * @return string|null
     */
    public function getClinicName();

    /**
     * Set ClinicName
     * @param string $clinicName
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setClinicName($clinicName);

    /**
     * Get GstNumber
     * @return string|null
     */
    public function getGstNumber();

    /**
     * Set GstNumber
     * @param string $gstNumber
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setGstNumber($gstNumber);

    /**
     * Get AddressLine
     * @return string|null
     */
    public function getAddressLine();

    /**
     * Set AddressLine
     * @param string $addressLine
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setAddressLine($addressLine);

    /**
     * Get Locality
     * @return string|null
     */
    public function getLocality();

    /**
     * Set Locality
     * @param string $locality
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setLocality($locality);

    /**
     * Get City
     * @return string|null
     */
    public function getCity();

    /**
     * Set City
     * @param string $city
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setCity($city);

    /**
     * Get Pincode
     * @return string|null
     */
    public function getPincode();

    /**
     * Set Pincode
     * @param string $pincode
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setPincode($pincode);

    /**
     * Get State
     * @return string|null
     */
    public function getState();

    /**
     * Set State
     * @param string $state
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setState($state);

    /**
     * Get SelectFacilities
     * @return string|null
     */
    public function getSelectFacilities();

    /**
     * Set SelectFacilities
     * @param string $selectFacilities
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setSelectFacilities($selectFacilities);

    /**
     * Get UploadCertificate
     * @return string|null
     */
    public function getUploadCertificate();

    /**
     * Set UploadCertificate
     * @param string $uploadCertificate
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setUploadCertificate($uploadCertificate);

    /**
     * Get ChequeImage
     * @return string|null
     */
    public function getChequeImage();

    /**
     * Set ChequeImage
     * @param string $chequeImage
     * @return \Zigly\Groomer\Api\Data\GroomerInterface
     */
    public function setChequeImage($chequeImage);
}

