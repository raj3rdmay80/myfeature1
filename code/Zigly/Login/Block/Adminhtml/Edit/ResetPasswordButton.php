<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Login
 */
namespace Zigly\Login\Block\Adminhtml\Edit;

/**
 * Class ResetPasswordButton
 */
class ResetPasswordButton extends \Magento\Customer\Block\Adminhtml\Edit\ResetPasswordButton
{
    /**
     * Retrieve button-specified settings
     *
     * @return array
     */
    public function getButtonData()
    {
        $customerId = $this->getCustomerId();
        $data = [];
        if ($customerId && false) {
            $data = [
                'label' => __('Reset Password'),
                'class' => 'reset reset-password',
                'on_click' => sprintf("location.href = '%s';", $this->getResetPasswordUrl()),
                'sort_order' => 60,
                'aclResource' => 'Magento_Customer::reset_password',
            ];
        }
        return $data;
    }
}
