<?php
declare(strict_types=1);

namespace Zigly\Mobilehome\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Mobilehome extends AbstractDb
{

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('zigly_mobilehome_mobilehome', 'mobilehome_id');
    }
}

