<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Login
 */
namespace Zigly\Login\Block\Widget;

use Magento\Customer\Block\Widget\Name as CustomerName;

/**
 * Widget for showing customer name.
 *
 * @method CustomerInterface getObject()
 * @method Name setObject(CustomerInterface $customer)
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Name extends CustomerName
{
    /**
     * @inheritdoc
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('Zigly_Login::widget/name.phtml');
    }

    /**
     * Define if LastName attribute can be shown
     *
     * @return bool
     */
    public function showLastName()
    {
        return $this->_isAttributeVisible('lastname');
        return;
    }

    /**
     * Check if attribute is visible
     *
     * @param string $attributeCode
     * @return bool
     */
    private function _isAttributeVisible($attributeCode)
    {
        $attributeMetadata = $this->_getAttribute($attributeCode);
        return $attributeMetadata ? (bool)$attributeMetadata->isVisible() : false;
    }

}
