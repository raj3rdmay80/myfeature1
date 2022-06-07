<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Login
 */

namespace Zigly\Login\Controller\Account;

use Zigly\Referral\Helper\Data;
use Zigly\Wallet\Model\WalletFactory;
use Magento\Customer\Model\CustomerFactory;
use Magento\Store\Model\ScopeInterface;
use Zigly\Referral\Model\ReferralFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Customer\Model\ResourceModel\Customer;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\AddressRegistry;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Customer\Model\AuthenticationInterface;
use Magento\Customer\Model\Customer\Mapper;
use Magento\Customer\Model\EmailNotificationInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\CustomerExtractor;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\State\UserLockedException;
use Magento\Customer\Controller\AbstractAccount;
use Magento\Framework\Phrase;

/**
 * Class EditPost
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class EditPost extends \Magento\Customer\Controller\Account\EditPost
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var ReferralFactory
     */
    protected $referralCustomer;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;
    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer
     */
    protected $customerResource;

    /**
     * Form code for data extractor
     */
    const FORM_DATA_EXTRACTOR_CODE = 'customer_account_edit';

    /**
     * @var AccountManagementInterface
     */
    protected $customerAccountManagement;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var Validator
     */
    protected $formKeyValidator;

    /**
     * @var CustomerExtractor
     */
    protected $customerExtractor;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var \Magento\Customer\Model\EmailNotificationInterface
     */
    private $emailNotification;

    /**
     * @var AuthenticationInterface
     */
    private $authentication;

    /**
     * @var Mapper
     */
    private $customerMapper;

    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @var AddressRegistry
     */
    private $addressRegistry;

    /**
     * @param Context $context
     * @param Data $helper
     * @param Customer $customerResource
     * @param WalletFactory $walletFactory
     * @param ReferralFactory $referralFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param CollectionFactory $collectionFactory
     * @param Session $customerSession
     * @param AccountManagementInterface $customerAccountManagement
     * @param CustomerRepositoryInterface $customerRepository
     * @param Validator $formKeyValidator
     * @param CustomerExtractor $customerExtractor
     * @param Escaper|null $escaper
     * @param AddressRegistry|null $addressRegistry
     */
    public function __construct(
        Context $context,
        Data $helper,
        StoreManagerInterface $storeManager,
        CustomerFactory $customerFactory,
        Customer $customerResource,
        WalletFactory $walletFactory,
        ReferralFactory $referralFactory,
        ScopeConfigInterface $scopeConfig,
        CollectionFactory $collectionFactory,
        Session $customerSession,
        AccountManagementInterface $customerAccountManagement,
        CustomerRepositoryInterface $customerRepository,
        Validator $formKeyValidator,
        CustomerExtractor $customerExtractor,
        ?Escaper $escaper = null,
        AddressRegistry $addressRegistry = null
    ) {
        parent::__construct($context, $customerSession, $customerAccountManagement, $customerRepository, $formKeyValidator, $customerExtractor);
        $this->helper = $helper;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->customerFactory = $customerFactory;
        $this->customerResource = $customerResource;
        $this->walletFactory = $walletFactory;
        $this->referralCustomer = $referralFactory;
        $this->collectionFactory = $collectionFactory;
        $this->session = $customerSession;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->customerRepository = $customerRepository;
        $this->formKeyValidator = $formKeyValidator;
        $this->customerExtractor = $customerExtractor;
        $this->escaper = $escaper ?: ObjectManager::getInstance()->get(Escaper::class);
        $this->addressRegistry = $addressRegistry ?: ObjectManager::getInstance()->get(AddressRegistry::class);
    }

    /**
     * Get authentication
     *
     * @return AuthenticationInterface
     */
    private function getAuthentication()
    {

        if (!($this->authentication instanceof AuthenticationInterface)) {
            return ObjectManager::getInstance()->get(
                \Magento\Customer\Model\AuthenticationInterface::class
            );
        } else {
            return $this->authentication;
        }
    }

    /**
     * Get email notification
     *
     * @return EmailNotificationInterface
     * @deprecated 100.1.0
     */
    private function getEmailNotification()
    {
        if (!($this->emailNotification instanceof EmailNotificationInterface)) {
            return ObjectManager::getInstance()->get(
                EmailNotificationInterface::class
            );
        } else {
            return $this->emailNotification;
        }
    }

    /**
     * @inheritDoc
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return null;
    }

    /**
     * Change customer email or password action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $validFormKey = $this->formKeyValidator->validate($this->getRequest());

        if (empty($this->_request->getParam('phone_number')) || !is_numeric($this->_request->getParam('phone_number')) || strlen($this->_request->getParam('phone_number')) > 10 || strlen($this->_request->getParam('phone_number')) < 10)
        {
            throw new InputException(__('Please enter valid phone number.'));
        }

        if ($validFormKey && $this->getRequest()->isPost()) {
            $currentCustomerDataObject = $this->getCustomerDataObject($this->session->getCustomerId());
            $customerCandidateDataObject = $this->populateNewCustomerDataObject(
                $this->_request,
                $currentCustomerDataObject
            );

            try {
                // whether a customer enabled change email option
                /*$this->processChangeEmailRequest($currentCustomerDataObject);*/
                /*$currentCustomerDataObject->setEmail($this->_request->getParam('email'));*/
                // whether a customer enabled change password option
                $isPasswordChanged = $this->changeCustomerPassword($currentCustomerDataObject->getEmail());

                // No need to validate customer address while editing customer profile
                $this->disableAddressValidation($customerCandidateDataObject);

                $this->customerRepository->save($customerCandidateDataObject);
                $store = $this->storeManager->getStore();
                $websiteId = $this->storeManager->getStore()->getWebsiteId();
                $customer = $this->customerFactory->create();
                $customer->setWebsiteId($websiteId)->loadByEmail($currentCustomerDataObject->getEmail());
                $customer->setWebsiteId($websiteId)
                        ->setStore($store)
                        ->setEmail($this->_request->getParam('email'))
                        ->setPhoneNumber($this->_request->getParam('phone_number'))
                        ->setForceConfirmed(true);
                $this->customerResource->save($customer);
                $this->getEmailNotification()->credentialsChanged(
                    $customerCandidateDataObject,
                    $currentCustomerDataObject->getEmail(),
                    $isPasswordChanged
                );
                $this->dispatchSuccessEvent($customerCandidateDataObject);
                $this->checkReferralForCustomer($this->_request, $currentCustomerDataObject);
                $this->messageManager->addSuccess(__('You saved the account information.'));
                return $resultRedirect->setPath('customer/account');
            } catch (InvalidEmailOrPasswordException $e) {
                $this->messageManager->addErrorMessage($this->escaper->escapeHtml($e->getMessage()));
            } catch (UserLockedException $e) {
                $message = __(
                    'The account sign-in was incorrect or your account is disabled temporarily. '
                    . 'Please wait and try again later.'
                );
                $this->session->logout();
                $this->session->start();
                $this->messageManager->addError($message);
                return $resultRedirect->setPath('customer/account/login');
            } catch (InputException $e) {
                $this->messageManager->addErrorMessage($this->escaper->escapeHtml($e->getMessage()));
                foreach ($e->getErrors() as $error) {
                    $this->messageManager->addErrorMessage($this->escaper->escapeHtml($error->getMessage()));
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('We can\'t save the customer.'));
            }

            $this->session->setCustomerFormData($this->getRequest()->getPostValue());
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('*/*/edit');
        return $resultRedirect;
    }

    /**
     * Account editing action completed successfully event
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customerCandidateDataObject
     * @return void
     */
    private function dispatchSuccessEvent(\Magento\Customer\Api\Data\CustomerInterface $customerCandidateDataObject)
    {
        $this->_eventManager->dispatch(
            'customer_account_edited',
            ['email' => $customerCandidateDataObject->getEmail()]
        );
    }

    /**
     * Get customer data object
     *
     * @param int $customerId
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    private function getCustomerDataObject($customerId)
    {
        return $this->customerRepository->getById($customerId);
    }

    /**
     * Create Data Transfer Object of customer candidate
     *
     * @param \Magento\Framework\App\RequestInterface $inputData
     * @param \Magento\Customer\Api\Data\CustomerInterface $currentCustomerData
     */
    public function checkReferralForCustomer(
        \Magento\Framework\App\RequestInterface $inputData,
        \Magento\Customer\Api\Data\CustomerInterface $currentCustomerData
    ) {
        if (!empty($inputData->getParam('referralcode')) && !empty($inputData->getParam('phone_number'))) {
            $referralcode = $inputData->getParam('referralcode'); 
            if($this->helper->isEnabled() && !$this->isCustomerExist($currentCustomerData->getId())) {
                if($this->helper->isValidReferralCode($referralcode)) {
                    $currentCustomerData->setCustomAttribute('referralcode', $referralcode);
                    $customerReferredValue = $this->scopeConfig->getValue("referral_program/options/value",ScopeInterface::SCOPE_STORE);
                    if (!empty($customerReferredValue)) {
                        $referredCustomer = $this->getCustomerByReferCode($referralcode);
                        $referralData = $this->referralCustomer->create();
                        $referralData->setReferredCustomerId($referredCustomer->getId());
                        $referralData->setReferenceId($referredCustomer->getRefercode());
                        $referralData->setReferralCustomerId($currentCustomerData->getId());
                        $referralData->setReferredValue($customerReferredValue);
                        $referralData->setReferredAmount($customerReferredValue);
                        $referralData->setReferralCustomerEmail($currentCustomerData->getEmail());
                        $referralData->save();
                        $walletTotalBalance =  0;
                        $walletTotal = $referredCustomer->getCustomAttribute('wallet_balance');
                        if($walletTotal != '' && $walletTotal != NULL) {
                            $walletTotalBalance = $walletTotal->getValue();
                        }
                        $model = $this->walletFactory->create();
                        $data['comment'] = "Referred Money";
                        $data['amount'] = $customerReferredValue;
                        $data['flag'] = 1;
                        $data['performed_by'] = "customer";
                        $data['visibility'] = 1;
                        $data['customer_id'] = $currentCustomerData->getId();
                        $model->setData($data);
                        $model->save();
                        $balance = $walletTotalBalance + $customerReferredValue;
                        $currentCustomerData->setCustomAttribute('wallet_balance',$balance);
                    }
                    $this->customerRepository->save($currentCustomerData);
                }
            }
            //Create reference code for this new customer
            $referCode = $this->helper->getReferCode();
            if($referCode) {
                $currentCustomerData->setCustomAttribute('refercode', $referCode);
                $this->customerRepository->save($currentCustomerData);
            }
        }
        return true;
    }

    /**
     * Create Data Transfer Object of customer candidate
     *
     * @param \Magento\Framework\App\RequestInterface $inputData
     * @param \Magento\Customer\Api\Data\CustomerInterface $currentCustomerData
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    private function populateNewCustomerDataObject(
        \Magento\Framework\App\RequestInterface $inputData,
        \Magento\Customer\Api\Data\CustomerInterface $currentCustomerData
    ) {
        $attributeValues = $this->getCustomerMapper()->toFlatArray($currentCustomerData);
        $customerDto = $this->customerExtractor->extract(
            self::FORM_DATA_EXTRACTOR_CODE,
            $inputData,
            $attributeValues
        );
        $customerDto->setId($currentCustomerData->getId());
        if (!$customerDto->getAddresses()) {
            $customerDto->setAddresses($currentCustomerData->getAddresses());
        }
        if (!$inputData->getParam('change_email')) {
            $customerDto->setEmail($currentCustomerData->getEmail());
        }

        return $customerDto;
    }

    private function getCustomerByReferCode($refercode = null)
    {
        $customerObj = $this->collectionFactory->create();
        $collection = $customerObj->addAttributeToSelect('*')
                    ->addAttributeToFilter('refercode',$refercode)
                    ->load();
        if($collection->count() > 0) {
            return $collection->getfirstItem();
        } else {
            return null;
        }
        return null;
    }

    private function isCustomerExist($customerId)
    {
        $referralCollection = $this->referralCustomer->create()->getCollection();
        $referralCollection->addFieldToFilter('referral_customer_id', $customerId);
        if($referralCollection->Count() > 1){
            return true;
        }
        return false;
    }

    /**
     * Change customer password
     *
     * @param string $email
     * @return boolean
     * @throws InvalidEmailOrPasswordException|InputException
     */
    protected function changeCustomerPassword($email)
    {
        $isPasswordChanged = false;
        if ($this->getRequest()->getParam('change_password')) {
            $currPass = $this->getRequest()->getPost('current_password');
            $newPass = $this->getRequest()->getPost('password');
            $confPass = $this->getRequest()->getPost('password_confirmation');
            if ($newPass != $confPass) {
                throw new InputException(__('Password confirmation doesn\'t match entered password.'));
            }

            $isPasswordChanged = $this->customerAccountManagement->changePassword($email, $currPass, $newPass);
        }

        return $isPasswordChanged;
    }

    /**
     * Process change email request
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $currentCustomerDataObject
     * @return void
     * @throws InvalidEmailOrPasswordException
     * @throws UserLockedException
     */
    private function processChangeEmailRequest(\Magento\Customer\Api\Data\CustomerInterface $currentCustomerDataObject)
    {
        if ($this->getRequest()->getParam('change_email')) {
            // authenticate a user for changing email
            try {
                $this->getAuthentication()->authenticate(
                    $currentCustomerDataObject->getId(),
                    $this->getRequest()->getPost('current_password')
                );
            } catch (InvalidEmailOrPasswordException $e) {
                throw new InvalidEmailOrPasswordException(
                    __("The Current password doesn't match this account. Verify the password and try again.")
                );
            }
        }
    }

    /**
     * Get Customer Mapper instance
     *
     * @return Mapper
     *
     * @deprecated 100.1.3
     */
    private function getCustomerMapper()
    {
        if ($this->customerMapper === null) {
            $this->customerMapper = ObjectManager::getInstance()->get(\Magento\Customer\Model\Customer\Mapper::class);
        }
        return $this->customerMapper;
    }

    /**
     * Disable Customer Address Validation
     *
     * @param CustomerInterface $customer
     * @throws NoSuchEntityException
     */
    private function disableAddressValidation($customer)
    {
        foreach ($customer->getAddresses() as $address) {
            $addressModel = $this->addressRegistry->retrieve($address->getId());
            $addressModel->setShouldIgnoreValidation(true);
        }
    }
}