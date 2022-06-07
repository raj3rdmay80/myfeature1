<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Login
 */
namespace Zigly\Login\Plugin\Social;

use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Store\Model\StoreManagerInterface;

class AbstractSocialPlugin
{
    public function beforeCreateCustomer(\Mageplaza\SocialLogin\Controller\Social\AbstractSocial $subject, $user, $type)
    {
        $user['firstname'] = $user['firstname'].' '.$user['lastname'];
        $user['lastname'] = "";
        return [$user, $type];
    }
}
