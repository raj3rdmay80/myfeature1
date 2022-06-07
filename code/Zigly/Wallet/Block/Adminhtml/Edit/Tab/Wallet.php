<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Wallet
 */
declare(strict_types=1);

namespace Zigly\Wallet\Block\Adminhtml\Edit\Tab;

use Magento\Customer\Controller\RegistryConstants;

class Wallet extends \Magento\Backend\Block\Template implements \Magento\Ui\Component\Layout\Tabs\TabInterface
{

    protected $_coreRegistry;

    /**
     * View constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }
 
    /**
     * @return string|null
     */
    public function getCustomerId()
    {
        $customerid = $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
        return $customerid;
    }
 
    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Wallet');
    }
 
    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Wallet');
    }
 
    /**
     * @return bool
     */
    public function canShowTab()
    {
        if ($this->getCustomerId() && $this->_authorization->isAllowed('Zigly_Wallet::Wallet_view')) {
            return true;
        }
        return false;
    }
 
    /**
     * @return bool
     */
    public function isHidden()
    {
        if ($this->getCustomerId()) {
            return false;
        }
        return true;
    }
 
    /**
     * Tab class getter
     *
     * @return string
     */
    public function getTabClass()
    {
        return '';
    }
 
    /**
     * Return URL link to Tab content
     *
     * @return string
     */
    public function getTabUrl()
    {
        return $this->getUrl('zigly_wallet/wallet/grid', ['_current' => true]);
    }
 
    /**
     * Tab should be loaded trough Ajax call
     *
     * @return bool
     */
    public function isAjaxLoaded()
    {
        return true;
    }
}