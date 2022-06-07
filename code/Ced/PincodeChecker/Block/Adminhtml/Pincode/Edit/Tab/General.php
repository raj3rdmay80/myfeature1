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

namespace Ced\PincodeChecker\Block\Adminhtml\Pincode\Edit\Tab;

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
        $model = $this->_coreRegistry->registry('pincode_data');
        if ($this->_isAllowedAction('Magento_Cms::save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Edit Item')]);


        $fieldset->addField(
            'zipcode',
            'text',
            [
                'name' => 'zipcode',
                'label' => __('Zipcode'),
                'title' => __('Zipcode'),
                'required' => true,
                'class' => 'validate-zip'
            ]
        );

        $fieldset->addField(
            'can_ship',
            'select',
            [
                'name' => 'can_ship',
                'label' => __('Shipment Available'),
                'title' => __('Shipment Available'),
                'required' => true,
                'onchange' => 'changecod(this)',
                'options' => ['1' => 'YES', '0' => 'NO']
            ]
        );

        $fieldset->addField(
            'can_cod',
            'select',
            [
                'name' => 'can_cod',
                'label' => __('COD Available'),
                'title' => __('COD Available'),
                'required' => true,
                'options' => ['1' => 'YES', '0' => 'NO']
            ]
        );

        $fieldset->addField(
            'days_to_deliver',
            'text',
            [
                'name' => 'days_to_deliver',
                'label' => __('Days To Deliver'),
                'title' => __('Days To Deliver'),
                'required' => true,
                'class' => 'validate-not-negative-number'
            ]
        );

        $script = $fieldset->addField(
            'vendor',
            'hidden',
            [
                'name' => 'vendor',
                'value' => 'admin'
            ]
        );

        $script->setAfterElementHtml("<script type=\"text/javascript\">
            function changecod(selectObject) {
                var option = selectObject.value;  
                var element = document.getElementById('page_can_cod');
                element.value = option;
            }require([
                'jquery',
                'jquery/ui', 
                'jquery/validate',
                'mage/translate'
                ], function($){
            jQuery.validator.addMethod(
                'validate-zip', function (value) { 
                    return value.match(/^[a-z0-9\-\s]+$/i);
                }, $.mage.__('Enter Valid Zipcode'));
            });
            </script>");

        $form->setValues($model->getData());
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

    public function getAvailableStatuses()
    {
        return [1 => __('Enabled'), 2 => __('Disabled')];
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
