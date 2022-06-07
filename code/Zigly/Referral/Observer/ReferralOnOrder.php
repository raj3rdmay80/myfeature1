<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Sales
 */
namespace Zigly\Referral\Observer;

use Zigly\Referral\Helper\Data;
use Zigly\Wallet\Model\WalletFactory;
use Magento\Framework\Event\Observer;
use Magento\Store\Model\ScopeInterface;
use Zigly\Referral\Model\ReferralFactory;
use Magento\Framework\Event\ObserverInterface;
use Zigly\Referral\Model\Source\ReferralOptions;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;

class ReferralOnOrder implements ObserverInterface
{
    /** @var CustomerRepositoryInterface */
    protected $customerRepository;

    protected $helper;

    protected $referralOptions;

    protected $referralCustomer;

    protected $scopeConfig;

    /**
     * @param Data $helper
     * @param WalletFactory $walletFactory
     * @param ReferralFactory $referralFactory
     * @param ReferralOptions $referralOptions
     * @param ScopeConfigInterface $scopeConfig
     * @param CollectionFactory $collectionFactory
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        Data $helper,
        WalletFactory $walletFactory,
        ReferralOptions $referralOptions,
        ReferralFactory $referralFactory,
        ScopeConfigInterface $scopeConfig,
        CollectionFactory $collectionFactory,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->helper = $helper;
        $this->scopeConfig = $scopeConfig;
        $this->walletFactory = $walletFactory;
        $this->referralOptions = $referralOptions;
        $this->referralCustomer = $referralFactory;
        $this->collectionFactory = $collectionFactory;
        $this->customerRepository = $customerRepository;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $order = $observer->getEvent()->getOrder();
        $customerId = $order->getCustomerId();
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/referral.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('It comes to the observer method');
        if(!$this->isCustomerExist($customerId) && $order->getStatus() == 'complete' && $this->helper->isEnabled()) {
            $referralCustomerId = $order->getCustomerId();
            $referralCustomerEmail = $order->getCustomerEmail();
            $firstname = $order->getCustomerFirstname();
            $lastname = $order->getCustomerLastname();
            $referralName = $firstname.' '.$lastname;
            $customer = $this->customerRepository->getById($referralCustomerId);

            if($customer->getCustomAttribute('referralcode') != '') {
                $refercode = $customer->getCustomAttribute('referralcode')->getValue();
                $referredCustomer = $this->getCustomerByReferCode($refercode);

                $referredCustomerId = $referredCustomer->getId();
                $referenceId = $referredCustomer->getRefercode();
                /*if($referredCustomer->getReferralType() == '' || $referredCustomer->getReferralType() == NULL) {
                    $referred_action_value = $this->scopeConfig->getValue("referral_program/options/action",ScopeInterface::SCOPE_STORE);
                    $referred_action_type = $this->referralOptions->getOptionText($referred_action_value);
                } else {
                    $referred_action_value = $referredCustomer->getReferralType();
                    $referred_action_type = $this->referralOptions->getOptionText($referred_action_value);
                }*/
                if($referredCustomer->getReferralValue() == '' || $referredCustomer->getReferralValue() == NULL){
                    $referredValue = $this->scopeConfig->getValue("referral_program/options/customer_value",ScopeInterface::SCOPE_STORE);
                } else {
                    $referredValue = $referredCustomer->getReferralValue();
                }
                //Calculate the referred amount
                $referredAmount = $referredValue;
                /*$referredAmount = 0; 
                if($referred_action_value == 1) {
                    $referredAmount = $referredValue;
                } else {
                    $referredAmount = number_format((($order->getGrandTotal() * $referredValue)/100), '2', '.', ',');
                }*/
                $logger->info('save to referral');
                $referralData = $this->referralCustomer->create();
                $referralData->setReferredCustomerId($referredCustomerId);
                $referralData->setReferenceId($referenceId);
                $referralData->setReferralCustomerId($referralCustomerId);
                /*$referralData->setReferredActionType($referred_action_type);*/
                $referralData->setReferredValue($referredValue);
                $referralData->setReferredAmount($referredAmount);
                $referralData->setReferralCustomerEmail($referralCustomerEmail);
                $referralData->setOrderId($order->getIncrementId());
                $referralData->save();

                /*
                //If we want to save the total earned customer refer amount
                $refer_total_value = 0;
                $referredCustomer = $this->customerRepository->getById($referredCustomerId);
                $refer_total = $referredCustomer->getCustomAttribute('refer_total_value');
                if($refer_total != '' && $refer_total != NULL) {
                    $refer_total_value = $refer_total->getValue();
                }
                $refer_total_value += $referredAmount;
                $referredCustomer->setCustomAttribute('refer_total_value', $refer_total_value);
                $this->customerRepository->save($referredCustomer);*/

                /*referred customer will get referred amount when referral place first order */
                if (!empty($referredCustomerId)) {
                    $logger->info('referred customer: '.$referredCustomerId);
                    $referredCustomer = $this->customerRepository->getById($referredCustomerId);
                    $walletTotalBalance =  0;
                    $walletTotal = $referredCustomer->getCustomAttribute('wallet_balance');
                    if($walletTotal != '' && $walletTotal != NULL) {
                        $walletTotalBalance = $walletTotal->getValue();
                    }
                    $referreAmount = $referredAmount;
                    $model = $this->walletFactory->create();
                    $data['comment'] = "Referred Money";
                    $data['amount'] = $referreAmount;
                    $data['flag'] = 1;
                    $data['performed_by'] = "customer";
                    $data['visibility'] = 1;
                    $data['customer_id'] = $referredCustomerId;
                    $model->setData($data);
                    $model->save();
                    $balance = $walletTotalBalance + $referreAmount;
                    $referredCustomer->setCustomAttribute('wallet_balance',$balance);
                    $this->customerRepository->save($referredCustomer);
                }
                $logger->info('Refer Code: '.$refercode);
                $logger->info('Referred Customer Id: '.$referredCustomerId);
                $logger->info('Referrence ID: '.$referenceId);
                /*$logger->info('Referrence Action Type: '.$referred_action_type);*/
                $logger->info('Referrence Value: '.$referredValue);
                $logger->info('Order Total: '.$order->getGrandTotal());
                $logger->info('Applied Referred Amount: '.$referredAmount);
                $logger->info('Total Earned Refer Amount: '.$referredAmount);
            }
        }
    } 

    private function getCustomerByReferCode($refercode = null) {
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

    private function isCustomerExist($customerId){
        $referralCollection = $this->referralCustomer->create()->getCollection();
        $referralCollection->addFieldToFilter('referral_customer_id', $customerId);
        if($referralCollection->count() > 1){
            return true;
        }
        return false;
    }
}