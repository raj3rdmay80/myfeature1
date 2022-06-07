<?php
/**
 * Copyright (C) 2020  Zigly
 * @package  Zigly_Managepets
 */
namespace Zigly\Managepets\Model;
class Managepets extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'zigly_managepets';

    protected $_cacheTag = 'zigly_managepets';

    protected $_eventPrefix = 'zigly_managepets';

    protected function _construct()
    {
        $this->_init('Zigly\Managepets\Model\ResourceModel\Managepets');
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
