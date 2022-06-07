<?php

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_PincodeChecker
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license     https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\PincodeChecker\Block\Adminhtml\Import\Edit\Tab;

/**
 * Cms page edit form main tab
 */
class General extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Prepare form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        if ($this->_isAllowedAction('Magento_Cms::save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('import', ['legend' => __('Import CSV')]);

        $fieldset->addField(
            'import_csv',
            'file',
            [
                'name' => 'import_csv',
                'label' => __('Import CSV'),
                'title' => __('Import CSV'),
                
            ]
        );

        $fieldset = $form->addFieldset('export', ['legend' => __('Export CSV')]);

        $script = $fieldset->addField(
            'export_csv',
            'button',
            [
                'name' => 'export_csv',
                'label' => __('Export CSV'),
                'title' => __('Export CSV'),
                'required' => false,
                /*'class' => 'action-default scalable',*/
                'value' => __('Export CSV'),
                'onclick' => "setLocation('" . $this->getUrl('*/*/export') . "')",
                'after_element_html' => '<p class="note"><span>Export the format of CSV Before Importing.<span></p>'
            ]
        );


        $script->setAfterElementHtml("<script type=\"text/javascript\">
            document.getElementById('save').onclick = function() {
                var imgVal = document.getElementById('page_import_csv'); 
                if(imgVal.files.length == 0) 
                { 
                    alert('No csv file has selected.'); 
                    return false; 
                }
                document.getElementById('import_form').submit();
            };
            </script>");

        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('General');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('General');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
