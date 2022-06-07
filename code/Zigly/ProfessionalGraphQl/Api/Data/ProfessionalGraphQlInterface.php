<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_ProfessionalGraphQl
 */
declare(strict_types=1);

namespace Zigly\ProfessionalGraphQl\Api\Data;

interface ProfessionalGraphQlInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const TOKEN = 'token';
    const IS_NEW = 'is_new';
    const PROFESSIONAL_STATUS = 'professional_status';
    const PROFESSIONAL_ROLE = 'professional_role';
    const OVERALL_RATING = 'overall_rating';
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
    const CHEQUE_IMAGE = 'cheque_image';
    const REGISTRATION_COUNCIL = 'registration_council';
    const REGISTRATION_NUMBER = 'registration_number';
    const YEAR_OF_REGISTRATION = 'year_of_registration';
    const YEAR_OF_EXPERIENCE = 'year_of_experience';
    const DOCTOR_MEMBERSHIP = 'doctor_membership';
    const AWARDS = 'awards';
    const SELECT_SPECIALISATION = 'select_specialisation';
    const SPECIALISATION_OTHER = 'specialisation_other';
    const CLINIC_NAME = 'clinic_name';
    const GST_NUMBER = 'gst_number';
    const ADDRESS_LINE = 'address_line';
    const LOCALITY = 'locality';
    const CITY = 'city';
    const PINCODE = 'pincode';
    const STATE = 'state';
    const SELECT_FACILITIES = 'select_facilities';
    const BD_NAME = 'bd_name';
    const BD_PROFILE_IMAGE = 'bd_profile_image';
    const BD_PHONE_NUMBER = 'bd_phone_number';
    const BD_EMAIL = 'bd_email';

    /**
     * Get professional_id
     * @return string|null
     */
    public function getProfessionalId();

    /**
     * Set professional_id
     * @param string $professionalId
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
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
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setProfileImage($profileImage);

    /**
     * Get name
     * @return string|null
     */
    public function getName();

    /**
     * Set name
     * @param string $name
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
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
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
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
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
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
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
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
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
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
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setStatus($status);

    /**
     * Get father_name
     * @return string|null
     */
    public function getFatherName();

    /**
     * Set father_name
     * @param string $fatherName
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
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
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
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
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
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
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
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
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
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
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
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
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
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
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
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
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
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
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
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
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
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
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
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
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
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
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
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
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
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
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
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
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
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
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
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
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
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
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
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
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
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
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
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
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
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
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
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
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
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
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
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
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
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
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
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
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setValueProductPurchased($valueProductPurchased);

    /**
     * Get token
     * @return string|null
     */
    public function getToken();

    /**
     * Set token
     * @param string $token
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setToken($token);

    /**
     * Get professional_status
     * @return string|null
     */
    public function getProfessionalStatus();

    /**
     * Set professional_status
     * @param string $professionalStatus
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setProfessionalStatus($professionalStatus);

    /**
     * Get professional_role
     * @return string|null
     */
    public function getProfessionalRole();

    /**
     * Set professional_role
     * @param string $professionalRole
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setProfessionalRole($professionalRole);

    /**
     * Get is_new
     * @return string|null
     */
    public function getIsNew();

    /**
     * Set is_new
     * @param string $isNew
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setIsNew($isNew);

    /**
     * Get universityCollege
     * @return string|null
     */
    public function getUniversityCollege();

    /**
     * Set universityCollege
     * @param string $universityCollege
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setUniversityCollege($universityCollege);

    /**
     * Get year of passing
     * @return string|null
     */
    public function getYearOfPassing();

    /**
     * Set year of passing
     * @param string $yearOfPassing
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
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
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
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
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
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
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
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
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
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
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
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
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
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
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setSelectSpecialisation($selectSpecialisation);

    /**
     * Get specialisationOther
     * @return string|null
     */
    public function getSpecialisationOther();

    /**
     * Set specialisationOther
     * @param string $specialisationOther
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setSpecialisationOther($specialisationOther);

    /**
     * Get ClinicName
     * @return string|null
     */
    public function getClinicName();

    /**
     * Set ClinicName
     * @param string $clinicName
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
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
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
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
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
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
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
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
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
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
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
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
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
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
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
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
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setUploadCertificate($uploadCertificate);

    /**
     * Get chequeImage
     * @return string|null
     */
    public function getChequeImage();

    /**
     * Set chequeImage
     * @param string $chequeImage
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setChequeImage($chequeImage);

    /**
     * Get bdName
     * @return string|null
     */
    public function getBdName();

    /**
     * Set bdName
     * @param string $bdName
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setBdName($bdName);

    /**
     * Get bdProfileImage
     * @return string|null
     */
    public function getBdProfileImage();

    /**
     * Set bdProfileImage
     * @param string $bdProfileImage
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setBdProfileImage($bdProfileImage);

    /**
     * Get bdPhoneNumber
     * @return string|null
     */
    public function getBdPhoneNumber();

    /**
     * Set bdPhoneNumber
     * @param string $bdPhoneNumber
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setBdPhoneNumber($bdPhoneNumber);

    /**
     * Get bdEmail
     * @return string|null
     */
    public function getBdEmail();

    /**
     * Set bdEmail
     * @param string $bdEmail
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setBdEmail($bdEmail);
}