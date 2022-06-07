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
 
namespace Ced\PincodeChecker\Block\Adminhtml\Import\Edit;

/**
 * Adminhtml cms page edit form block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'import_form', 
                        'action' => $this->getUrl('*/*/importpost', array('id' => $this->getRequest()->getParam('id'))), 
                        'method' => 'post',
                        'enctype' => 'multipart/form-data'
                        ]
            ]
        );
   
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
