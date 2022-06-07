<?php
declare(strict_types=1);

namespace Zigly\Mobilehome\Model;

use Magento\Framework\Model\AbstractModel;
use Zigly\Mobilehome\Api\Data\MobilehomeInterface;

class Mobilehome extends AbstractModel implements MobilehomeInterface
{

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Zigly\Mobilehome\Model\ResourceModel\Mobilehome::class);
    }

    /**
     * @inheritDoc
     */
    public function getMobilehomeId()
    {
        return $this->getData(self::MOBILEHOME_ID);
    }

    /**
     * @inheritDoc
     */
    public function setMobilehomeId($mobilehomeId)
    {
        return $this->setData(self::MOBILEHOME_ID, $mobilehomeId);
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }
}

