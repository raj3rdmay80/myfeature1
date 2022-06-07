<?php
declare(strict_types=1);

namespace Zigly\Mobilehome\Model\ResourceModel\Mobilehome;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'mobilehome_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \Zigly\Mobilehome\Model\Mobilehome::class,
            \Zigly\Mobilehome\Model\ResourceModel\Mobilehome::class
        );
    }
}

