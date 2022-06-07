<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Login
 */
namespace Zigly\Login\Model;
class Otpreport extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'zigly_otp_report';

    protected $_cacheTag = 'zigly_otp_report';

    protected $_eventPrefix = 'zigly_otp_report';

    protected function _construct()
    {
        $this->_init('Zigly\Login\Model\ResourceModel\Otpreport');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }
}
