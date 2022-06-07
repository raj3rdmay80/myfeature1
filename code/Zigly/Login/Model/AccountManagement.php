<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Login
 */
namespace Zigly\Login\Model;

use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\ValidationResultsInterfaceFactory;
use Magento\Customer\Helper\View as CustomerViewHelper;
use Magento\Customer\Model\Config\Share as ConfigShare;
use Magento\Customer\Model\Customer as CustomerModel;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Customer\Model\Metadata\Validator;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObjectFactory as ObjectFactory;
use Magento\Framework\Encryption\EncryptorInterface as Encryptor;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\EmailNotConfirmedException;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\State\UserLockedException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Math\Random;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Stdlib\StringUtils as StringHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Psr\Log\LoggerInterface as PsrLogger;
use Magestat\SigninPhoneNumber\Api\SigninInterface as HandlerSignin;
use Magestat\SigninPhoneNumber\Model\Config\Source\SigninMode;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\SerializerInterface;
// use Meat\Msg\Helper\SmsSender;

/**
 * Override Magento's default AccountManagement class.
 * @see \Magento\Customer\Model\AccountManagement
 */
class AccountManagement extends \Magento\Customer\Model\AccountManagement
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    // *
    //  * @var SmsSender

    // protected $smsSender;

    /**
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * @var ManagerInterface
     */
    private $eventManager;

    /**
     * @var SigninInterface
     */
    private $handlerSignin;

    /**
     * @var Curl
     */
    protected $curl;

    /**
     * @param CustomerFactory $customerFactory
     * @param ManagerInterface $eventManager
     * @param StoreManagerInterface $storeManager
     * @param Random $mathRandom
     * @param Validator $validator
     * @param ValidationResultsInterfaceFactory $validationResultsDataFactory
     * @param AddressRepositoryInterface $addressRepository
     * @param CustomerMetadataInterface $customerMetadataService
     * @param CustomerRegistry $customerRegistry
     * @param PsrLogger $logger
     * @param Encryptor $encryptor
     * @param ConfigShare $configShare
     * @param StringHelper $stringHelper
     * @param CustomerRepositoryInterface $customerRepository
     * @param ScopeConfigInterface $scopeConfig
     * @param TransportBuilder $transportBuilder
     * @param DataObjectProcessor $dataProcessor
     * @param Registry $registry
     * @param CustomerViewHelper $customerViewHelper
     * @param DateTime $dateTime
     * @param CustomerModel $customerModel
     * @param ObjectFactory $objectFactory
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @param HandlerSignin $handlerSignin
     * @param SmsSender $smsSender
     * @param curl $curl
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        CustomerFactory $customerFactory,
        ManagerInterface $eventManager,
        StoreManagerInterface $storeManager,
        Random $mathRandom,
        Validator $validator,
        ValidationResultsInterfaceFactory $validationResultsDataFactory,
        AddressRepositoryInterface $addressRepository,
        CustomerMetadataInterface $customerMetadataService,
        CustomerRegistry $customerRegistry,
        PsrLogger $logger,
        Encryptor $encryptor,
        ConfigShare $configShare,
        StringHelper $stringHelper,
        CustomerRepositoryInterface $customerRepository,
        ScopeConfigInterface $scopeConfig,
        TransportBuilder $transportBuilder,
        DataObjectProcessor $dataProcessor,
        Registry $registry,
        CustomerViewHelper $customerViewHelper,
        DateTime $dateTime,
        CustomerModel $customerModel,
        ObjectFactory $objectFactory,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        Curl $curl,
        \Zigly\Login\Helper\Smsdata $data,
        \Zigly\Login\Model\OtpreportFactory $otpreportFactory,
        SerializerInterface $serializer,
        // SmsSender $smsSender,
        HandlerSignin $handlerSignin
    ) {
        parent::__construct(
            $customerFactory,
            $eventManager,
            $storeManager,
            $mathRandom,
            $validator,
            $validationResultsDataFactory,
            $addressRepository,
            $customerMetadataService,
            $customerRegistry,
            $logger,
            $encryptor,
            $configShare,
            $stringHelper,
            $customerRepository,
            $scopeConfig,
            $transportBuilder,
            $dataProcessor,
            $registry,
            $customerViewHelper,
            $dateTime,
            $customerModel,
            $objectFactory,
            $extensibleDataObjectConverter
        );
        $this->customerRepository = $customerRepository;
        // $this->smsSender = $smsSender;
        $this->customerFactory = $customerFactory;
        $this->eventManager = $eventManager;
        $this->curl = $curl;
        $this->helper = $data;
        $this->otpreportFactory = $otpreportFactory;

        $this->serializer = $serializer;
        $this->handlerSignin = $handlerSignin;
    }

    /**
     * @inheritdoc
     */
    public function authenticate($username, $password)
    {

        try {
            if(is_numeric($username) &&  strlen($username) == 10) {
                $verifydata = $this->helper->verifyotp($username,$password);
                if($verifydata['status'] != 1){
                    $errormsg = $verifydata['msg'];
                    if ($errormsg == "OTP not match") {
                        $errormsg = "Please enter the correct OTP.";
                    }
                    throw new InvalidEmailOrPasswordException(__($errormsg));
                } else {
                    $number = '91'.$username;
                    $otpreport = $this->otpreportFactory->create()->getCollection()->addFieldToFilter('username',$number)->getFirstItem();
                    $record = $this->otpreportFactory->create()->load($otpreport->getEntityId());
                    $record->setData('flag', 1)->save();
                }
            } else {
                $username = str_replace(" ", "", $username);
                $otpreport = $this->otpreportFactory->create()->getCollection()->addFieldToFilter('username',$username)->getFirstItem();
                $errormsg = 'OTP not found';
                if($otpreport->getEntityId()){
                    $otpdata = $otpreport->getOtpvalue();
                    if($otpdata){
                        $additionalData = $this->serializer->unserialize($otpdata);
                        $expirycheck = time() - $additionalData['time'];
                        $failedTime = $otpreport->getData('otpfailedtime');
                        $expirymin =round(abs($expirycheck) / 60,2);
                        if($expirymin > (int)$additionalData['expiry']){
                            $errormsg = 'OTP expired';
                        }else{
                            $errormsg = '';
                            $record = $this->otpreportFactory->create()->load($otpreport->getEntityId());
                            if((int)$additionalData['otp'] != $password){
                                $errormsg = 'Please enter the correct OTP.';
                                $failedTime = $failedTime + 1;
                            } else {
                                $record->setData('flag', 1);
                            }
                            if($failedTime > 3){
                                $errormsg = 'You have exceeded the limit of the OTP!';
                            }
                            $record->setData('otpfailedtime', $failedTime)->save();
                        }
                    }else{
                        $errormsg = 'OTP not found';
                    }
                }
                if($errormsg){
                    throw new InvalidEmailOrPasswordException(__($errormsg));
                }
            }

            $customer = $this->handleSignin($username);
        } catch (NoSuchEntityException $e) {
            throw new InvalidEmailOrPasswordException(__('user-not-exists'));
        }

        $customerId = $customer->getId();
        if ($this->getAuthentication()->isLocked($customerId)) {
            throw new UserLockedException(__('The account is locked.'));
        }


        if ($customer->getConfirmation() && $this->isConfirmationRequired($customer)) {
            throw new EmailNotConfirmedException(__("This account isn't confirmed. Verify and try again."));
        }
        if(strlen($password) != 4) {
            $this->dispatchEvents($customer, $password);
        }
        return $customer;
    }

    /**
     * @inheritdoc
     */
    public function authenticateCustomerForApi($username, $password)
    {
        try {
            if(is_numeric($username) &&  strlen($username) == 10) {
                $verifydata = $this->helper->verifyotp($username,$password);
                if($verifydata['status'] != 1){
                    $errormsg = $verifydata['msg'];
                    if ($errormsg == "OTP not match") {
                        $errormsg = "Invalid";
                    }
                    throw new InvalidEmailOrPasswordException(__($errormsg));
                }
            } else {
                $username = str_replace(" ", "", $username);
                $otpreport = $this->otpreportFactory->create()->getCollection()->addFieldToFilter('username',$username)->getFirstItem();
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
                            if((int)$additionalData['otp'] != $password){
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
            $customer = $this->handleSignin($username);
            return $customer;
        } catch (NoSuchEntityException $e) {
            $notRegistered = "notExist";
            return $notRegistered;
        }
        $customerId = $customer->getId();
        if ($this->getAuthentication()->isLocked($customerId)) {
            throw new UserLockedException(__('The account is locked.'));
        }

        if ($customer->getConfirmation() && $this->isConfirmationRequired($customer)) {
            throw new EmailNotConfirmedException(__("This account isn't confirmed. Verify and try again."));
        }
        if(strlen($password) != 4) {
            $this->dispatchEvents($customer, $password);
        }
        return $customer;
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
     * {@inheritdoc}
     */
    private function getAuthentication()
    {
        if (!($this->authentication instanceof AuthenticationInterface)) {
            return \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Customer\Model\AuthenticationInterface::class
            );
        }
        return $this->authentication;
    }

    /**
     * @param CustomerInterface $customer
     * @param string $password Customer password.
     * @return AccountManagement
     */
    private function dispatchEvents($customer, $password)
    {
        $customerModel = $this->customerFactory->create()->updateData($customer);
        $this->eventManager->dispatch(
            'customer_customer_authenticated',
            ['model' => $customerModel, 'password' => $password]
        );
        $this->eventManager->dispatch(
            'customer_data_object_login',
            ['customer' => $customer]
        );
        return $this;
    }
}
