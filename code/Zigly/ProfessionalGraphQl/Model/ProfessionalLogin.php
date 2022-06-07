<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_ProfessionalGraphQl
 */
declare(strict_types=1);

namespace Zigly\ProfessionalGraphQl\Model;

use Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface;

class ProfessionalLogin extends \Magento\Framework\Api\AbstractExtensibleObject implements ProfessionalGraphQlInterface
{

    /**
     * Get professional_id
     * @return string|null
     */
    public function getProfessionalId()
    {
        return $this->_get(self::PROFESSIONAL_ID);
    }

    /**
     * Set professional_id
     * @param string $professionalId
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setProfessionalId($professionalId)
    {
        return $this->setData(self::PROFESSIONAL_ID, $professionalId);
    }

    /**
     * Get profile_image
     * @return string|null
     */
    public function getProfileImage()
    {
        return $this->_get(self::PROFILE_IMAGE);
    }

    /**
     * Set profile_image
     * @param string $profileImage
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setProfileImage($profileImage)
    {
        return $this->setData(self::PROFILE_IMAGE, $profileImage);
    }

    /**
     * Get name
     * @return string|null
     */
    public function getName()
    {
        return $this->_get(self::NAME);
    }

    /**
     * Set name
     * @param string $name
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * Get email
     * @return string|null
     */
    public function getEmail()
    {
        return $this->_get(self::EMAIL);
    }

    /**
     * Set email
     * @param string $email
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setEmail($email)
    {
        return $this->setData(self::EMAIL, $email);
    }

    /**
     * Get phone_number
     * @return string|null
     */
    public function getPhoneNumber()
    {
        return $this->_get(self::PHONE_NUMBER);
    }

    /**
     * Set phone_number
     * @param string $phoneNumber
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setPhoneNumber($phoneNumber)
    {
        return $this->setData(self::PHONE_NUMBER, $phoneNumber);
    }

    /**
     * Get onboard_date
     * @return string|null
     */
    public function getOnboardDate()
    {
        return $this->_get(self::ONBOARD_DATE);
    }

    /**
     * Set onboard_date
     * @param string $onboardDate
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setOnboardDate($onboardDate)
    {
        return $this->setData(self::ONBOARD_DATE, $onboardDate);
    }

    /**
     * Get city_hub
     * @return string|null
     */
    public function getCityHub()
    {
        return $this->_get(self::CITY_HUB);
    }

    /**
     * Set city_hub
     * @param string $cityHub
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setCityHub($cityHub)
    {
        return $this->setData(self::CITY_HUB, $cityHub);
    }

    /**
     * Get status
     * @return string|null
     */
    public function getStatus()
    {
        return $this->_get(self::STATUS);
    }

    /**
     * Set status
     * @param string $status
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Get father_name
     * @return string|null
     */
    public function getFatherName()
    {
        return $this->_get(self::FATHER_NAME);
    }

    /**
     * Set father_name
     * @param string $fatherName
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setFatherName($fatherName)
    {
        return $this->setData(self::FATHER_NAME, $fatherName);
    }

    /**
     * Get dob
     * @return string|null
     */
    public function getDob()
    {
        return $this->_get(self::DOB);
    }

    /**
     * Set dob
     * @param string $dob
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setDob($dob)
    {
        return $this->setData(self::DOB, $dob);
    }

    /**
     * Get gender
     * @return string|null
     */
    public function getGender()
    {
        return $this->_get(self::GENDER);
    }

    /**
     * Set gender
     * @param string $gender
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setGender($gender)
    {
        return $this->setData(self::GENDER, $gender);
    }

    /**
     * Get permanent_house_no
     * @return string|null
     */
    public function getPermanentHouseNo()
    {
        return $this->_get(self::PERMANENT_HOUSE_NO);
    }

    /**
     * Set permanent_house_no
     * @param string $permanentHouseNo
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setPermanentHouseNo($permanentHouseNo)
    {
        return $this->setData(self::PERMANENT_HOUSE_NO, $permanentHouseNo);
    }

    /**
     * Get permanent_locality
     * @return string|null
     */
    public function getPermanentLocality()
    {
        return $this->_get(self::PERMANENT_LOCALITY);
    }

    /**
     * Set permanent_locality
     * @param string $permanentLocality
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setPermanentLocality($permanentLocality)
    {
        return $this->setData(self::PERMANENT_LOCALITY, $permanentLocality);
    }

    /**
     * Get permanent_city
     * @return string|null
     */
    public function getPermanentCity()
    {
        return $this->_get(self::PERMANENT_CITY);
    }

    /**
     * Set permanent_city
     * @param string $permanentCity
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setPermanentCity($permanentCity)
    {
        return $this->setData(self::PERMANENT_CITY, $permanentCity);
    }

    /**
     * Get permanent_pincode
     * @return string|null
     */
    public function getPermanentPincode()
    {
        return $this->_get(self::PERMANENT_PINCODE);
    }

    /**
     * Set permanent_pincode
     * @param string $permanentPincode
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setPermanentPincode($permanentPincode)
    {
        return $this->setData(self::PERMANENT_PINCODE, $permanentPincode);
    }

    /**
     * Get permanent_state
     * @return string|null
     */
    public function getPermanentState()
    {
        return $this->_get(self::PERMANENT_STATE);
    }

    /**
     * Set permanent_state
     * @param string $permanentState
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setPermanentState($permanentState)
    {
        return $this->setData(self::PERMANENT_STATE, $permanentState);
    }

    /**
     * Get aadhar_img
     * @return string|null
     */
    public function getAadharImg()
    {
        return $this->_get(self::AADHAR_IMG);
    }

    /**
     * Set aadhar_img
     * @param string $aadharImg
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setAadharImg($aadharImg)
    {
        return $this->setData(self::AADHAR_IMG, $aadharImg);
    }

    /**
     * Get aadhar_img_back
     * @return string|null
     */
    public function getAadharImgBack()
    {
        return $this->_get(self::AADHAR_IMG_BACK);
    }

    /**
     * Set aadhar_img_back
     * @param string $aadharImgBack
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setAadharImgBack($aadharImgBack)
    {
        return $this->setData(self::AADHAR_IMG_BACK, $aadharImgBack);
    }

    /**
     * Get pan_img
     * @return string|null
     */
    public function getPanImg()
    {
        return $this->_get(self::PAN_IMG);
    }

    /**
     * Set pan_img
     * @param string $panImg
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setPanImg($panImg)
    {
        return $this->setData(self::PAN_IMG, $panImg);
    }

    /**
     * Get current_house_no
     * @return string|null
     */
    public function getCurrentHouseNo()
    {
        return $this->_get(self::CURRENT_HOUSE_NO);
    }

    /**
     * Set current_house_no
     * @param string $currentHouseNo
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setCurrentHouseNo($currentHouseNo)
    {
        return $this->setData(self::CURRENT_HOUSE_NO, $currentHouseNo);
    }

    /**
     * Get current_locality
     * @return string|null
     */
    public function getCurrentLocality()
    {
        return $this->_get(self::CURRENT_LOCALITY);
    }

    /**
     * Set current_locality
     * @param string $currentLocality
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setCurrentLocality($currentLocality)
    {
        return $this->setData(self::CURRENT_LOCALITY, $currentLocality);
    }

    /**
     * Get current_city
     * @return string|null
     */
    public function getCurrentCity()
    {
        return $this->_get(self::CURRENT_CITY);
    }

    /**
     * Set current_city
     * @param string $currentCity
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setCurrentCity($currentCity)
    {
        return $this->setData(self::CURRENT_CITY, $currentCity);
    }

    /**
     * Get current_pincode
     * @return string|null
     */
    public function getCurrentPincode()
    {
        return $this->_get(self::CURRENT_PINCODE);
    }

    /**
     * Set current_pincode
     * @param string $currentPincode
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setCurrentPincode($currentPincode)
    {
        return $this->setData(self::CURRENT_PINCODE, $currentPincode);
    }

    /**
     * Get current_state
     * @return string|null
     */
    public function getCurrentState()
    {
        return $this->_get(self::CURRENT_STATE);
    }

    /**
     * Set current_state
     * @param string $currentState
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setCurrentState($currentState)
    {
        return $this->setData(self::CURRENT_STATE, $currentState);
    }

    /**
     * Get account_details
     * @return string|null
     */
    public function getAccountDetails()
    {
        return $this->_get(self::ACCOUNT_DETAILS);
    }

    /**
     * Set account_details
     * @param string $accountDetails
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setAccountDetails($accountDetails)
    {
        return $this->setData(self::ACCOUNT_DETAILS, $accountDetails);
    }

    /**
     * Get account_holder_name
     * @return string|null
     */
    public function getAccountHolderName()
    {
        return $this->_get(self::ACCOUNT_HOLDER_NAME);
    }

    /**
     * Set account_holder_name
     * @param string $accountHolderName
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setAccountHolderName($accountHolderName)
    {
        return $this->setData(self::ACCOUNT_HOLDER_NAME, $accountHolderName);
    }

    /**
     * Get ifsc_code
     * @return string|null
     */
    public function getIfscCode()
    {
        return $this->_get(self::IFSC_CODE);
    }

    /**
     * Set ifsc_code
     * @param string $ifscCode
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setIfscCode($ifscCode)
    {
        return $this->setData(self::IFSC_CODE, $ifscCode);
    }

    /**
     * Get bank
     * @return string|null
     */
    public function getBank()
    {
        return $this->_get(self::BANK);
    }

    /**
     * Set bank
     * @param string $bank
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setBank($bank)
    {
        return $this->setData(self::BANK, $bank);
    }

    /**
     * Get company_name
     * @return string|null
     */
    public function getCompanyName()
    {
        return $this->_get(self::COMPANY_NAME);
    }

    /**
     * Set company_name
     * @param string $companyName
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setCompanyName($companyName)
    {
        return $this->setData(self::COMPANY_NAME, $companyName);
    }

    /**
     * Get gstin
     * @return string|null
     */
    public function getGstin()
    {
        return $this->_get(self::GSTIN);
    }

    /**
     * Set gstin
     * @param string $gstin
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setGstin($gstin)
    {
        return $this->setData(self::GSTIN, $gstin);
    }

    /**
     * Get total_rating
     * @return string|null
     */
    public function getTotalRating()
    {
        return $this->_get(self::TOTAL_RATING);
    }

    /**
     * Set total_rating
     * @param string $totalRating
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setTotalRating($totalRating)
    {
        return $this->setData(self::TOTAL_RATING, $totalRating);
    }

    /**
     * Get overall_rating
     * @return string|null
     */
    public function getOverallRating()
    {
        return $this->_get(self::OVERALL_RATING);
    }

    /**
     * Set overall_rating
     * @param string $overallRating
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setOverallRating($overallRating)
    {
        return $this->setData(self::OVERALL_RATING, $overallRating);
    }

    /**
     * Get credit_balance
     * @return string|null
     */
    public function getCreditBalance()
    {
        return $this->_get(self::CREDIT_BALANCE);
    }

    /**
     * Set credit_balance
     * @param string $creditBalance
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setCreditBalance($creditBalance)
    {
        return $this->setData(self::CREDIT_BALANCE, $creditBalance);
    }

    /**
     * Get completed_job
     * @return string|null
     */
    public function getCompletedJob()
    {
        return $this->_get(self::COMPLETED_JOB);
    }

    /**
     * Set completed_job
     * @param string $completedJob
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setCompletedJob($completedJob)
    {
        return $this->setData(self::COMPLETED_JOB, $completedJob);
    }

    /**
     * Get rejected_job
     * @return string|null
     */
    public function getRejectedJob()
    {
        return $this->_get(self::REJECTED_JOB);
    }

    /**
     * Set rejected_job
     * @param string $rejectedJob
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setRejectedJob($rejectedJob)
    {
        return $this->setData(self::REJECTED_JOB, $rejectedJob);
    }

    /**
     * Get cancelled_job
     * @return string|null
     */
    public function getCancelledJob()
    {
        return $this->_get(self::CANCELLED_JOB);
    }

    /**
     * Set cancelled_job
     * @param string $cancelledJob
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setCancelledJob($cancelledJob)
    {
        return $this->setData(self::CANCELLED_JOB, $cancelledJob);
    }

    /**
     * Get value_product_purchased
     * @return string|null
     */
    public function getValueProductPurchased()
    {
        return $this->_get(self::VALUE_PRODUCT_PURCHASED);
    }

    /**
     * Set value_product_purchased
     * @param string $valueProductPurchased
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setValueProductPurchased($valueProductPurchased)
    {
        return $this->setData(self::VALUE_PRODUCT_PURCHASED, $valueProductPurchased);
    }

    /**
     * Get token
     * @return string|null
     */
    public function getToken()
    {
        return $this->_get(self::TOKEN);
    }

    /**
     * Set token
     * @param string $token
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setToken($token)
    {
        return $this->setData(self::TOKEN, $token);
    }

    /**
     * Get is_new
     * @return string|null
     */
    public function getIsNew()
    {
        return $this->_get(self::IS_NEW);
    }

    /**
     * Set professional_status
     * @param string $professionalStatus
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setProfessionalStatus($professionalStatus)
    {
        return $this->setData(self::PROFESSIONAL_STATUS, $professionalStatus);
    }

    /**
     * Get professional_status
     * @return string|null
     */
    public function getProfessionalStatus()
    {
        return $this->_get(self::PROFESSIONAL_STATUS);
    }

    /**
     * Set professional_role
     * @param string $professionalRole
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setProfessionalRole($professionalRole)
    {
        return $this->setData(self::PROFESSIONAL_ROLE, $professionalRole);
    }

    /**
     * Get professional_role
     * @return string|null
     */
    public function getProfessionalRole()
    {
        return $this->_get(self::PROFESSIONAL_ROLE);
    }

    /**
     * Set is_new
     * @param string $isNew
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setIsNew($isNew)
    {
        return $this->setData(self::IS_NEW, $isNew);
    }


    /**
     * Get university_name
     * @return string|null
     */
    public function getUniversityCollege()
    {
        return $this->_get(self::UNIVERSITY_COLLEGE);
    }

    /**
     * Set universityCollege
     * @param string $universityCollege
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setUniversityCollege($universityCollege)
    {
        return $this->setData(self::UNIVERSITY_COLLEGE, $universityCollege);
    }

    /**
     * Get year of passing
     * @return string|null
     */
    public function getYearOfPassing()
    {
        return $this->_get(self::YEAR_OF_PASSING);
    }

    /**
     * Set year of passing
     * @param string $yearOfPassing
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setYearOfPassing($yearOfPassing)
    {
        return $this->setData(self::YEAR_OF_PASSING, $yearOfPassing);
    }

    /**
     * Get registration council
     * @return string|null
     */
    public function getRegistrationCouncil()
    {
        return $this->_get(self::REGISTRATION_COUNCIL);
    }

    /**
     * Set registration council
     * @param string $registrationCouncil
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setRegistrationCouncil($registrationCouncil)
    {
        return $this->setData(self::REGISTRATION_COUNCIL, $registrationCouncil);
    }

    /**
     * Get Registration Number
     * @return string|null
     */
    public function getRegistrationNumber()
    {
        return $this->_get(self::REGISTRATION_NUMBER);
    }

    /**
     * Set Registration Number
     * @param string $registrationNumber
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setRegistrationNumber($registrationNumber)
    {
        return $this->setData(self::REGISTRATION_NUMBER, $registrationNumber);
    }

    /**
     * Get YearOfRegistration
     * @return string|null
     */
    public function getYearOfRegistration()
    {
        return $this->_get(self::YEAR_OF_REGISTRATION);
    }

    /**
     * Set YearOfRegistration
     * @param string $yearOfRegistration
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setYearOfRegistration($yearOfRegistration)
    {
        return $this->setData(self::YEAR_OF_REGISTRATION, $yearOfRegistration);
    }

    /**
     * Get YearOfExperience
     * @return string|null
     */
    public function getYearOfExperience()
    {
        return $this->_get(self::YEAR_OF_EXPERIENCE);
    }

    /**
     * Set YearOfExperience
     * @param string $yearOfExperience
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setYearOfExperience($yearOfExperience)
    {
        return $this->setData(self::YEAR_OF_EXPERIENCE, $yearOfExperience);
    }

    /**
     * Get DoctorMembership
     * @return string|null
     */
    public function getDoctorMembership()
    {
        return $this->_get(self::DOCTOR_MEMBERSHIP);
    }

    /**
     * Set DoctorMembership
     * @param string $doctorMembership
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setDoctorMembership($doctorMembership)
    {
        return $this->setData(self::DOCTOR_MEMBERSHIP, $doctorMembership);
    }

    /**
     * Get awards
     * @return string|null
     */
    public function getAwards()
    {
        return $this->_get(self::AWARDS);
    }

    /**
     * Set awards
     * @param string $awards
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setAwards($awards)
    {
        return $this->setData(self::AWARDS, $awards);
    }

    /**
     * Get SelectSpecialisation
     * @return string|null
     */
    public function getSelectSpecialisation()
    {
        return $this->_get(self::SELECT_SPECIALISATION);
    }

    /**
     * Set SelectSpecialisation
     * @param string $selectSpecialisation
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setSelectSpecialisation($selectSpecialisation)
    {
        return $this->setData(self::SELECT_SPECIALISATION, $selectSpecialisation);
    }

    /**
     * Get specialisationOther
     * @return string|null
     */
    public function getSpecialisationOther()
    {
        return $this->_get(self::SPECIALISATION_OTHER);
    }

    /**
     * Set SpecialisationOther
     * @param string $specialisationOther
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setSpecialisationOther($specialisationOther)
    {
        return $this->setData(self::SPECIALISATION_OTHER, $specialisationOther);
    }

    /**
     * Get ClinicName
     * @return string|null
     */
    public function getClinicName()
    {
        return $this->_get(self::CLINIC_NAME);
    }

    /**
     * Set ClinicName
     * @param string $clinicName
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setClinicName($clinicName)
    {
        return $this->setData(self::CLINIC_NAME, $clinicName);
    }

    /**
     * Get GstNumber
     * @return string|null
     */
    public function getGstNumber()
    {
        return $this->_get(self::GST_NUMBER);
    }

    /**
     * Set GstNumber
     * @param string $gstNumber
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setGstNumber($gstNumber)
    {
        return $this->setData(self::GST_NUMBER, $gstNumber);
    }

    /**
     * Get AddressLine
     * @return string|null
     */
    public function getAddressLine()
    {
        return $this->_get(self::ADDRESS_LINE);
    }

    /**
     * Set AddressLine
     * @param string $addressLine
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setAddressLine($addressLine)
    {
        return $this->setData(self::ADDRESS_LINE, $addressLine);
    }

    /**
     * Get Locality
     * @return string|null
     */
    public function getLocality()
    {
        return $this->_get(self::LOCALITY);
    }

    /**
     * Set Locality
     * @param string $locality
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setLocality($locality)
    {
        return $this->setData(self::LOCALITY, $locality);
    }

    /**
     * Get City
     * @return string|null
     */
    public function getCity()
    {
        return $this->_get(self::CITY);
    }

    /**
     * Set City
     * @param string $city
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setCity($city)
    {
        return $this->setData(self::CITY, $city);
    }

    /**
     * Get Pincode
     * @return string|null
     */
    public function getPincode()
    {
        return $this->_get(self::PINCODE);
    }

    /**
     * Set Pincode
     * @param string $pincode
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setPincode($pincode)
    {
        return $this->setData(self::PINCODE, $pincode);
    }

    /**
     * Get State
     * @return string|null
     */
    public function getState()
    {
        return $this->_get(self::STATE);
    }

    /**
     * Set State
     * @param string $state
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setState($state)
    {
        return $this->setData(self::STATE, $state);
    }

    /**
     * Get SelectFacilities
     * @return string|null
     */
    public function getSelectFacilities()
    {
        return $this->_get(self::SELECT_FACILITIES);
    }

    /**
     * Set SelectFacilities
     * @param string $selectFacilities
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setSelectFacilities($selectFacilities)
    {
        return $this->setData(self::SELECT_FACILITIES, $selectFacilities);
    }

    /**
     * Get UploadCertificate
     * @return string|null
     */
    public function getUploadCertificate()
    {
        return $this->_get(self::UPLOAD_CERTIFICATE);
    }

    /**
     * Set UploadCertificate
     * @param string $uploadCertificate
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setUploadCertificate($uploadCertificate)
    {
        return $this->setData(self::UPLOAD_CERTIFICATE, $uploadCertificate);
    }

    /**
     * Get ChequeImage
     * @return string|null
     */
    public function getChequeImage()
    {
        return $this->_get(self::CHEQUE_IMAGE);
    }

    /**
     * Set ChequeImage
     * @param string $chequeImage
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setChequeImage($chequeImage)
    {
        return $this->setData(self::CHEQUE_IMAGE, $chequeImage);
    }

    /**
     * Get bdName
     * @return string|null
     */
    public function getBdName()
    {
        return $this->_get(self::BD_NAME);
    }

    /**
     * Set bdName
     * @param string $bdName
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setBdName($bdName)
    {
        return $this->setData(self::BD_NAME, $bdName);
    }

    /**
     * Get bdProfileImage
     * @return string|null
     */
    public function getBdProfileImage()
    {
        return $this->_get(self::BD_PROFILE_IMAGE);
    }

    /**
     * Set bdProfileImage
     * @param string $bdProfileImage
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setBdProfileImage($bdProfileImage)
    {
        return $this->setData(self::BD_PROFILE_IMAGE, $bdProfileImage);
    }

    /**
     * Get bdPhoneNumber
     * @return string|null
     */
    public function getBdPhoneNumber()
    {
        return $this->_get(self::BD_PHONE_NUMBER);
    }

    /**
     * Set bdPhoneNumber
     * @param string $bdPhoneNumber
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setBdPhoneNumber($bdPhoneNumber)
    {
        return $this->setData(self::BD_PHONE_NUMBER, $bdPhoneNumber);
    }

    /**
     * Get bdEmail
     * @return string|null
     */
    public function getBdEmail()
    {
        return $this->_get(self::BD_EMAIL);
    }

    /**
     * Set bdEmail
     * @param string $bdEmail
     * @return \Zigly\ProfessionalGraphQl\Api\Data\ProfessionalGraphQlInterface
     */
    public function setBdEmail($bdEmail)
    {
        return $this->setData(self::BD_EMAIL, $bdEmail);
    }

}