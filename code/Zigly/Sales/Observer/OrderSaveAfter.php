<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Sales
 */
namespace Zigly\Sales\Observer;

use Zigly\Referral\Helper\Data;
use Zigly\Wallet\Model\WalletFactory;
use Magento\Framework\Event\Observer;
use Magento\Store\Model\ScopeInterface;
use Zigly\Referral\Model\ReferralFactory;
use Magento\Framework\Event\ObserverInterface;
use Zigly\Referral\Model\Source\ReferralOptions;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class OrderSaveAfter implements ObserverInterface
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
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        Data $helper,
        WalletFactory $walletFactory,
        ReferralOptions $referralOptions,
        ReferralFactory $referralFactory,
        ScopeConfigInterface $scopeConfig,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->helper = $helper;
        $this->scopeConfig = $scopeConfig;
        $this->walletFactory = $walletFactory;
        $this->referralOptions = $referralOptions;
        $this->referralCustomer = $referralFactory;
        $this->customerRepository = $customerRepository;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $order = $observer->getEvent()->getOrder();
        if ($order->getStatus() == 'complete') {
            $now = new \DateTime('now');
            $writer = new \Laminas\Log\Writer\Stream(BP . '/var/log/Completed-'.$now->format('d-m-Y').'.log');
            $logger = new \Laminas\Log\Logger();
            $logger->addWriter($writer);
            $logger->info('------------------START-----------------');
            $orderItems = $order->getAllItems();
            foreach ($orderItems as $item) {
                if ($item->getReturnPolicy()) {
                    $logger->info('getReturnPolicy'.$item->getReturnPolicy());
                    $now = new \DateTime('now');
                    $addDays = 'P'.$item->getReturnPolicy().'D';
                    $now->add(new \DateInterval($addDays));
                    $logger->info('setReturnPolicyDATE'.$now->format('Y-m-d H:i:s'));
                    $item->setReturnPolicyDate($now->format('Y-m-d H:i:s'));
                    $item->save();
                }
            }
            $now = new \DateTime('now');
            $order->setCompletedAt($now->format('Y-m-d H:i:s'))->save();
            $logger->info('completed at : '.$order->getCompletedAt());
            $logger->info('------------------END-----------------');
        }
    }
}