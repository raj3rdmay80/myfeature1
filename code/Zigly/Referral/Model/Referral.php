<?php
namespace Zigly\Referral\Model;

class Referral extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init('Zigly\Referral\Model\ResourceModel\Referral');
    }
}
?>