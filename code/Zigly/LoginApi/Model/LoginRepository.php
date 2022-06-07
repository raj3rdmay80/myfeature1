<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_LoginAPi
 */
declare(strict_types=1);

namespace Zigly\LoginApi\Model;

use Webkul\MobikulCore\Helper\Data;
use Webkul\MobikulCore\Model\OauthTokenFactory;
use Zigly\Login\Helper\Smsdata;
use Zigly\Login\Model\OtpreportFactory;
use Zigly\Login\Model\AccountManagement;
use Magento\Customer\Model\AccountManagementApi;
use Magento\Customer\Api\Data\CustomerInterface;
use Zigly\LoginApi\Api\LoginRepositoryInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magestat\SigninPhoneNumber\Model\Config\Source\SigninMode;
use Magento\Integration\Api\CustomerTokenServiceInterface;
use Magento\Integration\Model\Oauth\TokenFactory as TokenModelFactory;
use Magestat\SigninPhoneNumber\Api\SigninInterface as HandlerSignin;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;

class LoginRepository implements LoginRepositoryInterface
{

    /**
     * Customer Account Service
     *
     * @var AccountManagement
     */
    private $customerAccountManagement;

    /**
     * @var Data
     */
    private $helper;

    /**
     * @var OauthTokenFactory
     */
    protected $authTokenFactory;

    /**
     * @var CustomerTokenServiceInterface
     */
    private $tokenService;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var Smsdata
     */
    private $helperData;

    /**
     * @var \Magento\Customer\Api\Data\CustomerInterfaceFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * Token Model
     *
     * @var TokenModelFactory
     */
    private $tokenModelFactory;

   
    protected $userImageFactory;

    /**
     * @param Data $helper
     * @param Smsdata $helperData
     * @param OauthTokenFactory $authTokenFactory
     * @param TokenModelFactory $tokenModelFactory
     * @param CustomerInterfaceFactory $customerFactory
     * @param StoreManagerInterface $storeManager
     * @param DataObjectHelper $dataObjectHelper
     * @param AccountManagement $customerAccountManagement
     * @param CustomerRepositoryInterface $customerRepository
     * @param HandlerSignin $handlerSignin
     * @param SerializerInterface $serializer
     * @param OtpreportFactory $otpreportFactory
     * @param AccountManagementApi $accountManagement
     */


    public function __construct(
        Data $helper,
        Smsdata $helperData,
        OauthTokenFactory $authTokenFactory,
        TokenModelFactory $tokenModelFactory,
        CustomerInterfaceFactory $customerFactory,
        StoreManagerInterface $storeManager,
        CustomerRepositoryInterface $customerRepository,
        HandlerSignin $handlerSignin,
        DataObjectHelper $dataObjectHelper,
        SerializerInterface $serializer,
        OtpreportFactory $otpreportFactory,
        AccountManagementApi $accountManagement,
        AccountManagement $customerAccountManagement,
        \Webkul\MobikulCore\Model\UserImageFactory $userImageFactory,
        CustomerCollectionFactory $customerCollectionFactory,
        CustomerTokenServiceInterface $tokenService
    ) {
        $this->authTokenFactory = $authTokenFactory;
        $this->helper = $helper;
        $this->helperData = $helperData;
        $this->serializer = $serializer;
        $this->tokenService = $tokenService;
        $this->customerRepository = $customerRepository;
        $this->handlerSignin = $handlerSignin;
        $this->otpreportFactory = $otpreportFactory;
        $this->accountManagement = $accountManagement;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->customerFactory = $customerFactory;
        $this->storeManager = $storeManager;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->tokenModelFactory = $tokenModelFactory;
        $this->userImageFactory = $userImageFactory;
        $this->customerCollectionFactory = $customerCollectionFactory;
    }

    /**
     * Send Login Otp
     * @param string $username
     * @return \Zigly\LoginApi\Api\Data\LoginResponseInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sendOtp($username)
    {
        try {
            $customerData = [];
            $customerData['status'] = false;
            if (isset($username) && !empty($username) && is_numeric($username)){
                if (strlen($username) > 10 || strlen($username) < 10) {
                    throw new NoSuchEntityException(__('Please enter a valid phone number.'));
                }
                $loginOtp = $this->helperData->sendloginotp($username);
                $customerData['status'] = ($loginOtp['status'] == 1) ? true : false;
                $customerData['message'] = "Otp has been sent";
            } elseif (isset($username) && !empty($username) && !is_numeric($username)) {
                if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
                    throw new NoSuchEntityException(__('Please enter a valid email.'));
                }
                $otp = rand(1000,9999);
                $dataser = ['otp' => $otp,'time' => time(),'expiry' => 5,'atttime' => 0];
                $additionalData = $this->serializer->serialize($dataser);
                $username = str_replace(" ", "", $username);
                $otpreport = $this->otpreportFactory->create()->getCollection()->addFieldToFilter('username',$username)->getFirstItem();
                if($otpreport->getEntityId()){
                    $record = $this->otpreportFactory->create()->load($otpreport->getEntityId());
                    $record->setUsername($username)->setOtpvalue($additionalData)->save();
                }else{
                    $this->otpreportFactory->create()->setUsername($username)->setOtpvalue($additionalData)->save();
                }
                $templatevariable['name'] = '';
                $templatevariable['email'] = $username;
                $templatevariable['otp'] = $otp;
                $this->helperData->sendmail($templatevariable);
                $customerData['status'] = true;
                $customerData['message'] = "Otp has been sent";
            }
            $response = new \Magento\Framework\DataObject();
            $response->setStatus($customerData['status']);
            $response->setMessage($customerData['message']);
            return $response;
        } catch (AuthenticationException $e) {
            throw new NoSuchEntityException(__($e->getMessage()), $e);
        }
    }

    /**
     * Resend Login Otp
     * @param string $username
     * @return \Zigly\LoginApi\Api\Data\LoginResponseInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function resendOtp($username)
    {
        try {
            $customerData = [];
            $customerData['status'] = false;
            if (isset($username) && !empty($username) && is_numeric($username)){
                if (strlen($username) > 10 || strlen($username) < 10) {
                    throw new NoSuchEntityException(__('Please enter a valid phone number.'));
                }
                $loginOtp = $this->helperData->resendloginotp($username);
                $customerData['status'] = $loginOtp['status'];
                if ($loginOtp['msg'] == "otp_expired") {
                    $customerData['message'] = "OTP expired";
                } elseif ($loginOtp['msg'] == "OTP retry count maxed out") {
                    $customerData['message'] = "You have exceeded the limit of resending the OTP! Please try again after 5 mins";
                } else {
                    $customerData['message'] = $loginOtp['msg'];
                }
            } elseif (isset($username) && !empty($username) && !is_numeric($username)) {
                if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
                    throw new NoSuchEntityException(__('Please enter a valid email.'));
                }
                $otp = rand(1000,9999);
                $dataser = ['otp' => $otp,'time' => time(),'expiry' => 5,'atttime' => 0];
                $additionalData = $this->serializer->serialize($dataser);
                $username = str_replace(" ", "", $username);
                $otpreport = $this->otpreportFactory->create()->getCollection()->addFieldToFilter('username',$username)->getFirstItem();
                if ($otpreport->getEntityId()){
                    $otpdata = $otpreport->getOtpvalue();
                    if ($otpdata) {
                        $additionalData = $this->serializer->unserialize($otpdata);
                        $atttime =(int)$additionalData['atttime'];
                        if($atttime >= 3){
                            throw new NoSuchEntityException(__('You have exceeded the limit of resending the OTP! Please try again after 5 mins.'));
                        } else {
                            $expirycheck = time() - $additionalData['time'];
                            $expirymin =round(abs($expirycheck) / 60,2);
                            if($expirymin > (int)$additionalData['expiry']){
                                throw new NoSuchEntityException(__('OTP expired'));
                            } else {
                                $otp =(int)$additionalData['otp'];
                                $templatevariable['name'] = '';
                                $templatevariable['email'] = $username;
                                $templatevariable['otp'] = $additionalData['otp'];
                                $atttime = (int)$additionalData['atttime'] + 1;
                                $dataser = ['otp' => $additionalData['otp'],'time' => time(),'expiry' => 5,'atttime' => $atttime];
                                $additionalData = $this->serializer->serialize($dataser);
                                $record = $this->otpreportFactory->create()->load($otpreport->getEntityId());
                                $record->setUsername($username)->setOtpvalue($additionalData)->save();
                                $this->helperData->sendmail($templatevariable);
                                $customerData['status'] = true;
                                $customerData['message'] = "Resent the otp";
                            }
                        }
                    } else {
                        throw new NoSuchEntityException(__('OTP not found'));
                    }
                }else{
                    throw new NoSuchEntityException(__('OTP not found'));
                }
            }
            $response = new \Magento\Framework\DataObject();
            $response->setStatus($customerData['status']);
            $response->setMessage($customerData['message']);
            return $response;
        } catch (AuthenticationException $e) {
            throw new NoSuchEntityException(__($e->getMessage()), $e);
        }
    }

    /**
     * Login Otp and generate token
     * @param string $username
     * @param string $otp
     * @param string $type
     * @return \Zigly\LoginApi\Api\Data\LoginResponseInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function customerLogin($username, $otp = null, $type)
    {
        try {


            $customerCollection = $this->customerCollectionFactory->create()->addAttributeToSelect('*')->addAttributeToFilter('phone_number',$username);
             if($customerCollection->count()){
                $customerData = $customerCollection->getData(); 
               echo $customerId = $customerData[0]['entity_id'];

                $userImageModel = $this->userImageFactory->create();
                $collection = $userImageModel->getCollection()->addFieldToFilter("customer_id", $customerId);
                echo "count: ".$collection->count();
                if($collection->count()){
                    $imgpathColl = $collection->getData();
                    print_r($imgpathColl);
                    exit;
                    $photoPath = $imgpathColl[0]['profile'];
                }else{  $photoPath = '++';   }
                
            }
            else{ $photoPath = '----';  }

            $customerData = [];
            $customerData['message'] = "No customer found";
            $token['mobile_token'] = "false";
            if (isset($username) && !empty($username) && is_numeric($username)){
                if (strlen($username) > 10 || strlen($username) < 10) {
                    throw new NoSuchEntityException(__('Please enter a valid phone number.'));
                }
            } elseif (isset($username) && !empty($username) && !is_numeric($username)) {
                if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
                    throw new NoSuchEntityException(__('Please enter a valid email.'));
                }
            }
            if (!isset($type) || empty($type)) {
                throw new NoSuchEntityException(__('Please enter login type.'));
            }
            if ($type == "general") {
                if (!isset($otp) || empty($otp) || !is_numeric($otp) || strlen($otp) > 4 || strlen($otp) < 4) {
                    throw new NoSuchEntityException(__('Please enter valid OTP.'));
                }
                /*$token = $this->customerTokenService->createCustomerAccessToken($username, $otp);*/
                $customerDataObject = $this->customerAccountManagement->authenticateCustomerForApi($username, $otp);
                if ($customerDataObject == "notExist") {
                    $token['token'] = "false";
                    $token['is_customer'] = "false";
                } elseif ($customerDataObject == "Invalid") {
                    throw new AuthenticationException(__('Invalid otp.'));
                } else {
                    $token['is_customer'] = "true";
                    $token['token'] = $this->tokenModelFactory->create()->createCustomerToken($customerDataObject->getId())->getToken();
                    $id = (int)$customerDataObject->getId();
                    $token['mobile_token'] = $this->getCustomerTokenById($id);
                }
            } elseif ($type == "social") {
                try {
                    $customer = $this->handleSignin($username);
                    $token['token'] = $this->tokenModelFactory->create()->createCustomerToken($customer->getId())->getToken();
                    $id = (int)$customer->getId();
                    $token['mobile_token'] = $this->getCustomerTokenById($id);
                    $token['is_customer'] = "true";
                } catch (NoSuchEntityException $e) {
                    $token['token'] = "false";
                    $token['is_customer'] = "false";
                }
            }
            $customerData['status'] = true;
            if ($token['is_customer'] == "true") {
                $customerData['message'] = "Login successful";
            }
            $response = new \Magento\Framework\DataObject();
            $response->setToken($token['token']);
            $response->setMobileToken($token['mobile_token']);
            $response->setIsCustomer($token['is_customer']);
            $response->setStatus($customerData['status']);
            $response->setMessage($customerData['message']);
            $response->setPhotoPath($photoPath);
            return $response;
        } catch (AuthenticationException $e) {
            throw new NoSuchEntityException(__($e->getMessage()), $e);
        }
    }

    /**
     * Get Customer Token
     *
     * @param integer $id
     * @return string|null
     */
    private function getCustomerTokenById(int $id)
    {
        $token = $this->authTokenFactory->create()->load(
            $id,
            "customer_id"
        )->getToken();
        if (!$token) {
            $token = $this->helper->createCustomerAccessToken($id);
        }
        return $token;
    }

    /**
     * Create customer and generate token
     * @param mixed $customer
     * @return \Zigly\LoginApi\Api\Data\LoginResponseInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createCustomer($customer)
    {
        try {
            $customerData = [];
            $customerData['status'] = false;
            $token['token'] = "false";
            $token['mobile_token'] = "false";
            $customerData['message'] = "Customer not created";
            if (isset($customer['username']) && !empty($customer['username']) && is_numeric($customer['username'])){
                if (strlen($customer['username']) > 10 || strlen($customer['username']) < 10) {
                    throw new NoSuchEntityException(__('Please enter a valid phone number.'));
                }
            } elseif (isset($customer['username']) && !empty($customer['username']) && !is_numeric($customer['username'])) {
                if (!filter_var($customer['username'], FILTER_VALIDATE_EMAIL)) {
                    throw new NoSuchEntityException(__('Please enter a valid email.'));
                }
            }
            if (!isset($customer['type']) || empty($customer['type'])) {
                throw new NoSuchEntityException(__('Please enter login type.'));
            }
            if ($customer['type'] == "general") {
                if (!isset($customer['otp']) || empty($customer['otp']) || !is_numeric($customer['otp']) || strlen($customer['otp']) > 4 || strlen($customer['otp']) < 4) {
                    throw new NoSuchEntityException(__('Please enter valid OTP.'));
                }
                if(is_numeric($customer['username']) &&  strlen($customer['username']) == 10) {
                    $verifydata = $this->helperData->verifyotp($customer['username'],$customer['otp']);
                    if($verifydata['status'] != 1){
                        $errormsg = $verifydata['msg'];
                        if ($errormsg == "OTP not match") {
                            $errormsg = "Invalid";
                        }
                        throw new InvalidEmailOrPasswordException(__($errormsg));
                    }
                } else {
                    $customer['username'] = str_replace(" ", "", $customer['username']);
                    $otpreport = $this->otpreportFactory->create()->getCollection()->addFieldToFilter('username',$customer['username'])->getFirstItem();
                    $errormsg = '';
                    if($otpreport->getEntityId()){
                        $otpdata = $otpreport->getOtpvalue();
                        if($otpdata){
                            $additionalData = $this->serializer->unserialize($otpdata);
                            $expirycheck = time() - $additionalData['time'];
                            $expirymin =round(abs($expirycheck) / 60,2);
                            if($expirymin > (int)$additionalData['expiry']){
                                $errormsg = 'OTP expired';
                            }else{
                                $errormsg = '';
                                if((int)$additionalData['otp'] != $customer['otp']){
                                    $errormsg = 'Invalid';
                                }
                            }
                        }else{
                            $errormsg = 'OTP not found';
                        }
                    }
                    if($errormsg){
                        throw new InvalidEmailOrPasswordException(__($errormsg));
                    }
                }
                $customerDataObject = $this->customerFactory->create();
                $this->dataObjectHelper->populateWithArray(
                    $customerDataObject,
                    $customer,
                    \Magento\Customer\Api\Data\CustomerInterface::class
                );
                $store = $this->storeManager->getStore();
                $storeId = $store->getId();
                $customerDataObject->setWebsiteId($store->getWebsiteId());
                $customerDataObject->setStoreId($storeId);
                $data = $this->accountManagement->createAccount($customerDataObject);
                /*$token = $this->customerTokenService->createCustomerAccessToken($customer['username'], $customer['otp']);*/
                /*$customerDataObjects = $this->customerAccountManagement->authenticateCustomerForApi($customer['username'], $customer['otp']);*/
                $token['token'] = $this->tokenModelFactory->create()->createCustomerToken($data->getId())->getToken();
                $id = (int)$data->getId();
                $token['mobile_token'] = $this->getCustomerTokenById($id);
                $customerData['status'] = true;
                $customerData['message'] = "Customer created";
            } elseif ($customer['type'] == "social") {
                $customerDataObject = $this->customerFactory->create();
                $this->dataObjectHelper->populateWithArray(
                    $customerDataObject,
                    $customer,
                    \Magento\Customer\Api\Data\CustomerInterface::class
                );
                $store = $this->storeManager->getStore();
                $storeId = $store->getId();
                $customerDataObject->setWebsiteId($store->getWebsiteId());
                $customerDataObject->setStoreId($storeId);
                $data = $this->accountManagement->createAccount($customerDataObject);
                $token['token'] = $this->tokenModelFactory->create()->createCustomerToken($data->getId())->getToken();
                $id = (int)$data->getId();
                $token['mobile_token'] = $this->getCustomerTokenById($id);
                $customerData['status'] = true;
                $customerData['message'] = "Customer created";
            }
            $response = new \Magento\Framework\DataObject();
            $response->setToken($token['token']);
            $response->setMobileToken($token['mobile_token']);
            $response->setStatus($customerData['status']);
            $response->setMessage($customerData['message']);
            return $response;
        } catch (AuthenticationException $e) {
            throw new NoSuchEntityException(__($e->getMessage()), $e);
        }
    }

    /**
     * Handle login mode.
     *
     * @param string $username Customer email or phone number
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    public function handleSignin(string $username)
    {
        if (!$this->handlerSignin->isEnabled()) {
            return $this->customerRepository->get($username);
        }

        switch ($this->handlerSignin->getSigninMode()) {
            case SigninMode::TYPE_PHONE:
                return $this->withPhoneNumber($username);
            case SigninMode::TYPE_BOTH_OR:
                return $this->withPhoneNumberOrEmail($username);
            default:
                return $this->customerRepository->get($username);
        }
    }

    /**
     * Action to login with Phone Number only.
     *
     * @param string $username
     * @return CustomerInterface
     * @throws NoSuchEntityException
     */
    private function withPhoneNumber(string $username)
    {
        $customer = $this->handlerSignin->getByPhoneNumber($username);
        if (false == $customer) {
            throw new NoSuchEntityException();
        }
        return $customer;
    }

    /**
     * Action to login with Phone Number or Email.
     *
     * @param string $username
     * @return CustomerInterface
     */
    private function withPhoneNumberOrEmail(string $username)
    {
        $customer = $this->handlerSignin->getByPhoneNumber($username);
        if (false == $customer) {
            return $this->customerRepository->get($username);
        }
        return $customer;
    }

    /**
     * revoke customer login
     * @param int $customerId
     * @return \Zigly\LoginApi\Api\Data\LoginResponseInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function revokeCustomerAccessToken($customerId)
    {
        try {
            $customerData = [];
            if (!isset($customerId) || empty($customerId) || !is_numeric($customerId)) {
                throw new NoSuchEntityException(__('Please enter a valid customer id.'));
            }
            try {
                $this->tokenService->revokeCustomerAccessToken($customerId);
                $customerData['status'] = true;
                $customerData['message'] = "Logout successful";
            } catch (NoSuchEntityException $e) {
                $customerData['status'] = false;
                $customerData['message'] = false;
            }
            $response = new \Magento\Framework\DataObject();
            $response->setStatus($customerData['status']);
            $response->setMessage($customerData['message']);
            return $response;
        } catch (AuthenticationException $e) {
            throw new NoSuchEntityException(__($e->getMessage()), $e);
        }
    }
}