<?php
namespace Zigly\Referral\Model\ResourceModel;

class Referral extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('referral_data', 'referral_id');
    }
}
?>