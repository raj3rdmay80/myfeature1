<?php
namespace Zigly\Referral\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Customer\Api\CustomerRepositoryInterface;

class ReferralSave implements ObserverInterface
{
    /** @var CustomerRepositoryInterface */
    protected $customerRepository;

    protected $helper;

    /**
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        \Zigly\Referral\Helper\Data $helper
    ) {
        $this->customerRepository = $customerRepository;
        $this->helper = $helper;
    }

    public function execute(Observer $observer)
    {
        
        $request = $observer->getRequest();
        $customer_data = $observer->getCustomer();
        $customer = $this->customerRepository->getById($customer_data->getId());
		$data = $request->getPost('customer');

        if($this->helper->isEnabled()) {
    		if(!$data) return $this;

            //Check the customer was already have the referral code and was referred by another one valid customer. If not, Save the given referral code provided by admin user on the customer create/edit form.
            if($customer_data->getId()){

                $referral_code = $customer->getCustomAttribute('referralcode');
                $referral_code_value = '';
                if($referral_code != '') {
                    $referral_code_value = $referral_code->getValue();
                }
                if($data['referralcode'] != '' && ($referral_code_value != NULL || $referral_code_value != '')){
                    if($this->helper->isValidReferralCode($data['referralcode'])) {
                        $customer->setCustomAttribute('referralcode', $data['referralcode']);
                    }
                }
            }
            
            //Check the customer has reference code already. If not create it.
            $refercode = $customer->getCustomAttribute('refercode');
            $refer_code_value = '';
                if($refer_code_value != '') {
                    $refer_code_value = $refercode->getValue();
                }
            if($customer_data->getId() && $refer_code_value == ''){
                if($this->isValidReferCode($data['refercode'])) {
                    $customer->setCustomAttribute('refercode', $data['refercode']);
                } else {
                    $refer_code_value = $this->helper->getReferCode();
                    $customer->setCustomAttribute('refercode', $refer_code_value);
                }
            }

            if($data['referral_type'] != ''){
                $customer->setCustomAttribute('referral_type', $data['referral_type']);
            }
            
            if($data['referral_value'] != ''){
                $customer->setCustomAttribute('referral_value', $data['referral_value']);
            }
        }
        $this->customerRepository->save($customer);
        return $this;
    }

    public function isValidReferCode($referCode){
        if(strlen($referCode) == 7) {
            return true;
        } else {
            return false;
        }
    }
}