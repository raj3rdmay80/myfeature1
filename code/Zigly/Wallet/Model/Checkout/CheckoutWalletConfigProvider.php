<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Wallet
 */
declare(strict_types=1);

namespace Zigly\Wallet\Model\Checkout;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Customer\Model\SessionFactory as CustomerSession;
use Zigly\Wallet\Helper\Data as WalletHelper;

class CheckoutWalletConfigProvider implements ConfigProviderInterface
{
    /**
     * Customer session model
     *
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var WalletHelper
     */
    private $walletHelper;

    /**
     * @param CustomerSession $customerSession
     */
    public function __construct(
        WalletHelper $walletHelper,
        CustomerSession $customerSession
    ) {
        $this->walletHelper = $walletHelper;
        $this->customerSession = $customerSession;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $result = [];

        $customer = $this->customerSession->create()->getCustomer();
        if (!is_null($customer->getWalletBalance()) && $this->walletHelper->isEnabled()) {
            $totalBalance = $customer->getWalletBalance();
            $result['zwallet_amount'] = (int)$totalBalance;
        }
        return $result;
    }
}
