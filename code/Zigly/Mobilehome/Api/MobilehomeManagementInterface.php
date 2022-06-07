<?php
declare(strict_types=1);

namespace Zigly\Mobilehome\Api;

interface MobilehomeManagementInterface
{

    /**
        * @api 
        * @return \Zigly\Mobilehome\Api\Data\MobilehomeInterface
        * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getMobilehome();
}

