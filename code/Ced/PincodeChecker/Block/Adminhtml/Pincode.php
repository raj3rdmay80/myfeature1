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
 
namespace Ced\PincodeChecker\Block\Adminhtml;

class Pincode extends \Magento\Backend\Block\Widget\Container
{
	 /**
     * @var string
     */
    protected $_template = 'pincode/index.phtml';
 
    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
		$this->getAddButtonOptions();
    }
 

    /**
     * Prepare button and grid
     *
     * @return \Magento\Catalog\Block\Adminhtml\Product
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'grid',
            $this->getLayout()->createBlock('Ced\PincodeChecker\Block\Adminhtml\Pincode\Grid', 'ced.pincode.grid')
        );
        return parent::_prepareLayout();
    }
 
 
    /**
     *
     *
     * @return array
     */
    protected function getAddButtonOptions()
    {
		$add_new_button = [
            'label' => __('Add New'),
			'class' => 'primary',
            'onclick' => "setLocation('" . $this->_getCreateUrl() . "')"
        ];
        $import_csv = [
            'label' => __('Import CSV'),
            'class' => 'primary',
            'onclick' => "setLocation('" . $this->_getImportUrl() . "')"
        ];
        $this->buttonList->add('import_csv', $import_csv);
		$this->buttonList->add('add_new_button', $add_new_button);	   	
    }
 
    /**
     *
     *
     * @param string $type
     * @return string
     */
    protected function _getCreateUrl()
    {
        return $this->getUrl('*/*/new' );
    }

    protected function _getImportUrl()
    {
        return $this->getUrl('*/*/import' );
    }
 
    /**
     * Render grid
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }
}
?>