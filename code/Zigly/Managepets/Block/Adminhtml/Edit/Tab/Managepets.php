<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Managepets
 */
namespace Zigly\Managepets\Block\Adminhtml\Edit\Tab;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Ui\Component\Layout\Tabs\TabInterface;
use Zigly\Managepets\Model\ResourceModel\Managepets\CollectionFactory;
/**
 * Customer account form block
 */
class Managepets extends \Magento\Backend\Block\Template implements TabInterface
{
    /**
     * Template
     *
     * @var string
     */

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $data);
    }
    /**
     * @return string|null
     */
    public function getCustomerId()
    {
        $customerid = $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
        if(!$customerid){
            $customerid = $this->getManualCustomerId();
            if(!$customerid){
                $customerid = $this->getRequest()->getParam('id');
            }
        }
        return $customerid;
    }
    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Manage Pets');
    }
    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Manage Pets');
    }
    /**
     * @return bool
     */
    public function canShowTab()
    {
        if ($this->getCustomerId() && $this->_authorization->isAllowed('Zigly_Managepets::Managepets_view')) {
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
        return $this->getUrl('managepets/*/manage', ['_current' => true]);
        // return '';
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

