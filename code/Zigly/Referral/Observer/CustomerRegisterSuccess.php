<?php
namespace Zigly\Referral\Observer;

use Zigly\Referral\Helper\Data;
use Zigly\Wallet\Model\WalletFactory;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Zigly\Referral\Model\ReferralFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;

class CustomerRegisterSuccess implements ObserverInterface
{
    /** @var CustomerRepositoryInterface */
    protected $customerRepository;

    protected $helper;

    protected $scopeConfig;

    protected $referralCustomer;

    /**
     * @param Data $helper
     * @param WalletFactory $walletFactory
     * @param ReferralFactory $referralFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param CollectionFactory $collectionFactory
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        Data $helper,
        WalletFactory $walletFactory,
        ReferralFactory $referralFactory,
        ScopeConfigInterface $scopeConfig,
        CollectionFactory $collectionFactory,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->helper = $helper;
        $this->scopeConfig = $scopeConfig;
        $this->walletFactory = $walletFactory;
        $this->referralCustomer = $referralFactory;
        $this->collectionFactory = $collectionFactory;
        $this->customerRepository = $customerRepository;
    }

    public function execute(Observer $observer)
    {
        $accountController = $observer->getAccountController();
        $customer = $observer->getCustomer();
        $request = $accountController->getRequest();
        $referralcode = $request->getParam('referralcode');
        if($this->helper->isEnabled()) {
            if($referralcode != NULL && $referralcode != '') {
                if($this->helper->isValidReferralCode($referralcode)) {
                    $customer->setCustomAttribute('referralcode', $referralcode);
                    $customerReferredValue = $this->scopeConfig->getValue("referral_program/options/value",ScopeInterface::SCOPE_STORE);
                    if (!empty($customerReferredValue) && $this->isCustomerExist($referralcode)) {
                        $referredCustomer = $this->getCustomerByReferCode($referralcode);
                        $referralData = $this->referralCustomer->create();
                        $referralData->setReferredCustomerId($referredCustomer->getId());
                        $referralData->setReferenceId($referredCustomer->getRefercode());
                        $referralData->setReferralCustomerId($customer->getId());
                        $referralData->setReferredValue($customerReferredValue);
                        $referralData->setReferredAmount($customerReferredValue);
                        $referralData->setReferralCustomerEmail($customer->getEmail());
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
                        $data['customer_id'] = $customer->getId();
                        $model->setData($data);
                        $model->save();
                        $balance = $walletTotalBalance + $customerReferredValue;
                        $customer->setCustomAttribute('wallet_balance',$balance);
                    }
                    $this->customerRepository->save($customer);
                }
            }
            //Create reference code for this new customer
            $referCode = $this->helper->getReferCode();
            if($referCode) {
                $customer->setCustomAttribute('refercode', $referCode);
                $this->customerRepository->save($customer);
            }
        }
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

    private function isCustomerExist($refercode = null){
        $customerObj = $this->collectionFactory->create();
        $approved = 'approved';
        $collection = $customerObj->addAttributeToSelect('*')
                    ->addAttributeToFilter('refercode',$refercode)
                    ->addAttributeToFilter('is_approved', $approved)
                    ->load();
        if($collection->count() > 0){
            return true;
        }
        return false;
    }
}