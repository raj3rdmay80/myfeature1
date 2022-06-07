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
  
namespace Ced\PincodeChecker\Block\Adminhtml\Pincode;
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Initialize cms page edit block
     *
     * @return void
     */
	
  protected $_coreRegistry = null;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    public function _construct()
    { 
        $this->_objectId   = 'id';
        $this->_blockGroup = 'Ced_PincodeChecker';
        $this->_controller = 'adminhtml_pincodechecker';
        
        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save'));
        $this->buttonList->update('delete', 'label', __('Delete'));
    }

    
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('pincode_data')->getId()) {
            return __("Edit Item '%1'", $this->escapeHtml($this->_coreRegistry->registry('pincode_data')->getTitle()));
        } else {
            return __('New Item');
        }
    }

    
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('grid/*/save', ['_current' => true, 'back' => 'edit', 'active_tab' => '{{tab_id}}']);
    }

  
    protected function _prepareLayout()
    {
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('page_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'content');
                }
            };
        ";
        return parent::_prepareLayout();
    }
}
