<?php
declare(strict_types=1);

namespace Zigly\ScheduleManagementApi\Model;

use Magento\Framework\Model\AbstractModel;
use Zigly\ScheduleManagementApi\Api\Data\GroomingHubInterface;

class GroomingHub extends AbstractModel implements GroomingHubInterface
{

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Zigly\ScheduleManagementApi\Model\ResourceModel\GroomingHub::class);
    }

    /**
     * @inheritDoc
     */
    public function getGroominghubId()
    {
        return $this->getData(self::GROOMINGHUB_ID);
    }

    /**
     * @inheritDoc
     */
    public function setGroominghubId($groominghubId)
    {
        return $this->setData(self::GROOMINGHUB_ID, $groominghubId);
    }

    /**
     * @inheritDoc
     */
    public function getHubId()
    {
        return $this->getData(self::HUB_ID);
    }

    /**
     * @inheritDoc
     */
    public function setHubId($hubId)
    {
        return $this->setData(self::HUB_ID, $hubId);
    }
}

