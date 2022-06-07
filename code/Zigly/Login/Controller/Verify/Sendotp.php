<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Login
 */
namespace Zigly\Login\Controller\Verify;

use Zigly\Login\Helper\Smsdata;
use Magento\Framework\HTTP\Client\Curl;
use Zigly\Login\Model\OtpreportFactory;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magestat\SigninPhoneNumber\Model\Config\Source\SigninMode;
use Magestat\SigninPhoneNumber\Api\SigninInterface as HandlerSignin;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;


class Sendotp extends Action
{

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    public function __construct(
        Curl $curl,
        Context $context,
        Smsdata $helperdata,
        HandlerSignin $handlerSignin,
        JsonFactory $resultJsonFactory,
        PageFactory $resultPageFactory,
        SerializerInterface $serializer,
        CustomerFactory $customerFactory,
        ScopeConfigInterface $scopeConfig,
        OtpreportFactory $otpreportFactory,
        StoreManagerInterface $storeManager,
        CustomerRepositoryInterface $customerRepository,
        CustomerCollectionFactory $customerCollectionFactory,
        AccountManagementInterface $customerAccountManagement
    ) {
        $this->curl = $curl;
        $this->helper = $helperdata;
        $this->serializer = $serializer;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->handlerSignin = $handlerSignin;
        $this->customerFactory = $customerFactory;
        $this->otpreportFactory = $otpreportFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->customerCollectionFactory = $customerCollectionFactory;
        parent::__construct($context);
    }


    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $data = $this->getRequest()->getPostValue();
        $username = $data['username'];
        $oldPhoneNumber = $data['oldphonenumber'];
        $oldEmail = $data['oldemail'];
        $templatevariable['otp'] = 0;
        $templatevariable['msg'] = '';
        $templatevariable['status'] = 0;
        $templatevariable['checkaccount'] = 0;
        if ($username) {
            $customer = $this->handleSignin($username);
            if ($customer) {
                $templatevariable['status'] = 0;
                if(!is_numeric($username)) {
                    $templatevariable['msg'] = 'There is already an account with this email address';
                } else {
                    $templatevariable['msg'] = 'There is already an account with this phone number';
                }
                $result->setData($templatevariable);
                return $result;
            }
        }
        $websiteId = (int)$this->storeManager->getWebsite()->getId();
        if(!is_numeric($username) && $oldEmail != $username){
            $otp = rand(1000,9999);
            $staticEmails = explode(",", $this->helper->getStaticOtpEmail());
            if (count($staticEmails) && in_array($username, $staticEmails)) {
                $otp = 1200;
            }
            $dataser = ['otp' => $otp,'time' => time(),'expiry' => 10,'atttime' => 0];
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
            $this->helper->sendmail($templatevariable);
            $templatevariable['status'] = 1;
        }else{
            if (is_numeric($username) && $username != $oldPhoneNumber && strlen($username) == 10) {
                $templatevariable = $this->helper->sendloginotp($username);
            }
        }
        $resultPage = $this->resultPageFactory->create();
        unset($templatevariable['otp']);
        $result->setData($templatevariable);
        return $result;
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
            if (is_numeric($username)) {
                return false;
            } else {
                /*return $this->customerRepository->get($username);*/
                $websiteId = (int)$this->storeManager->getWebsite()->getId();
                $isEmailNotExists = $this->customerAccountManagement->isEmailAvailable($username, $websiteId);
                if ($isEmailNotExists){
                    return false;
                } else {
                    return true;
                }
            }
        }
        return $customer;
    }
}