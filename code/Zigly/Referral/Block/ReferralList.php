<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Referral
 */
declare(strict_types=1);

namespace Zigly\Referral\Block;

use \Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\Template\Context;
use Zigly\Referral\Model\ResourceModel\Referral\CollectionFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;

class ReferralList extends Template
{
    
    protected $_template = 'Zigly_Referral::referral_friend.phtml';

    /**
     * @var CollectionFactory
     */
    protected $referralCollectionFactory;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepositoryInterface;

    /**
     * @var Session
     */
    protected $customerSession;

    protected $referrals;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

       /**
     * @param Context $context
     * @param CollectionFactory $referralCollectionFactory
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     * @param ScopeConfigInterface $scopeConfig
     * @param Session $customerSession
     */
    public function __construct(
        Context $context,
        CollectionFactory $referralCollectionFactory,
        CustomerRepositoryInterface $customerRepositoryInterface,
        ScopeConfigInterface $scopeConfig,
        Session $customerSession,
        array $data = []
    ) {
        $this->referralCollectionFactory = $referralCollectionFactory;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->customerSession = $customerSession;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('My Referrals'));
    }

    
    public function getReferrals()
    {
        if (!($customerId = $this->customerSession->getCustomerId())) {
            return false;
        }
        if (!$this->referrals) {
            $this->referrals = $this->referralCollectionFactory->create()
                ->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'referred_customer_id', ['eq' => $customerId]
            )->setOrder(
                'created_at',
                'desc'
            );
        }
        return $this->referrals;
    }

    /**
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getReferrals()) {
            $pager = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                'referrals.history.pager'
            )->setCollection(
                $this->getReferrals()
            );
            $this->setChild('pager', $pager);
            $this->getReferrals()->load();
        }
        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getReferenceId()
    {
        if (($customerId = $this->customerSession->getCustomerId())) {
            $customer = $this->customerRepositoryInterface->getById($customerId);
            if($customer->getCustomAttribute('refercode') != '') {
                $refercode = $customer->getCustomAttribute('refercode')->getValue();
                return $refercode;
            } else {
                return '';
            }
        } else {
            return '';
        }
    }

    public function getReferralDiscription()
    {
        $referralFields=array('{{referee}}',"{{referrer}}");
        $refereeValue= $this->scopeConfig->getValue('referral_program/options/value', ScopeInterface::SCOPE_STORE);
        $referrerValue= $this->scopeConfig->getValue('referral_program/options/customer_value', ScopeInterface::SCOPE_STORE);
        $discription= $this->scopeConfig->getValue('referral_program/options/description', ScopeInterface::SCOPE_STORE);
        $replace_string = array($refereeValue,$referrerValue);
        return  str_replace($referralFields, $replace_string, $discription);
    }

    public function getReferralWhatsappDiscription($reference_id, $link)
    {
        $referralFields=array("{{referralcode}}","{{referee}}","{{signup}}");
        $refereeValue= $this->scopeConfig->getValue('referral_program/options/value', ScopeInterface::SCOPE_STORE);
        $discription= $this->scopeConfig->getValue('referral_program/options/whatsapp_description', ScopeInterface::SCOPE_STORE);
        $replace_string = array($reference_id, $refereeValue, $link);
        return  str_replace($referralFields, $replace_string, $discription);
    }

    public function getCustomerReferralValue()
    {
        return $this->scopeConfig->getValue('referral_program/options/customer_value', ScopeInterface::SCOPE_STORE);
    }
}
