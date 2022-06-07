<?php

namespace Zigly\Referral\Helper;

use Magento\Framework\Math\Random;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $mathRandom;

    /**
    * @var ScopeConfigInterface
    */
    protected $scopeConfig;

    /**
    * referral config path
    */
    const REFERRAL_IS_ENABLED = 'referral_program/options/enable_referral';

    /**
     * @param Random $mathRandom
     * @param ScopeConfigInterface $scopeConfig
    */
    public function __construct(
        Random $mathRandom,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->mathRandom = $mathRandom;
        $this->scopeConfig = $scopeConfig;
    }

    /*
    * get referral is enabled
    */
    public function isEnabled()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::REFERRAL_IS_ENABLED, $storeScope);
    }

    // Generate Token String
    public function generateToken($length,  $chars = null)
    {
        return $this->mathRandom->getRandomString($length, $chars);
    }

    //Check whether the referred customer is a existing customer or not
    public function isValidReferralCode($referralCode = '')
    {
        if ($referralCode == '') {
            return false;
        }
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerObj = $objectManager->create('Magento\Customer\Model\ResourceModel\Customer\Collection');
        $collection = $customerObj->addAttributeToSelect('*')
            ->addAttributeToFilter('refercode', $referralCode)
            ->load();
        if ($collection->count() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function isAvailable($referId)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerObj = $objectManager->create('Magento\Customer\Model\ResourceModel\Customer\Collection');
        $collection = $customerObj->addAttributeToSelect('*')
            ->addAttributeToFilter('refercode', $referId)
            ->load();
        if ($collection->count() > 0) {
            return false;
        } else {
            return true;
        }
    }

    public function getReferCode()
    {
        $referId = $this->generateToken(7);
        if ($this->isAvailable($referId)) {
            return $referId;
        } else {
            return $this->getReferCode();
        }
    }

    public function getReferId($length)
    {
        $random_string = '';
        for ($i = 0; $i < $length; $i++) {
            $number = random_int(0, 36);
            $character = base_convert($number, 10, 36);
            $random_string .= $character;
        }

        return $random_string;
    }
}
