<?php
declare(strict_types=1);

namespace Zigly\ScheduleManagementApi\Model;

use Magento\Framework\Model\AbstractModel;
use Zigly\ScheduleManagementApi\Api\Data\GroomingHubPincodeInterface;

class GroomingHubPincode extends AbstractModel implements GroomingHubPincodeInterface
{

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Zigly\ScheduleManagementApi\Model\ResourceModel\GroomingHubPincode::class);
    }

    /**
     * @inheritDoc
     */
    public function getGroominghubpincodeId()
    {
        return $this->getData(self::GROOMINGHUBPINCODE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setGroominghubpincodeId($groominghubpincodeId)
    {
        return $this->setData(self::GROOMINGHUBPINCODE_ID, $groominghubpincodeId);
    }

    /**
     * @inheritDoc
     */
    public function getPincodeId()
    {
        return $this->getData(self::PINCODEID);
    }

    /**
     * @inheritDoc
     */
    public function setPincodeId($pincodeId)
    {
        return $this->setData(self::PINCODEID, $pincodeId);
    }
}

