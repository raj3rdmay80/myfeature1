<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Wallet
 */
namespace Zigly\Wallet\Controller\Adminhtml\Wallet;

class Grid extends \Magento\Customer\Controller\Adminhtml\Index
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Zigly_Wallet::Wallet_view';

    public function execute()
    {
        $this->initCurrentCustomer();
        $resultLayout = $this->resultLayoutFactory->create();
        return $resultLayout;
    }
}
