<?php
 
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_PincodeChecker
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */ 
  
namespace Ced\PincodeChecker\Block\Adminhtml\Pincode\Edit;

/**
 * Admin page left menu
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
    	
        parent::_construct();
        $this->setId('pincode_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('General Information'));
    }
    protected function _beforeToHtml()
    {
    	$this->addTab(
    			'main',
    			[
    			'label' => __('General'),
    			'title' => __('General'),
    			'content' => $this->getLayout()->createBlock('Ced\PincodeChecker\Block\Adminhtml\Pincode\Edit\Tab\General')->toHtml(),
    			'active' => true
    			]
    	);
    	
    	return parent::_beforeToHtml();
    }
}

