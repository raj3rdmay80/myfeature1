<?php
/**
 * Customer Grid Collection
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Zigly\GroomingService\Model\ResourceModel\Groomer;

class Collection extends \Zigly\Groomer\Model\ResourceModel\Groomer\Collection
{
    /**
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addFieldToFilter('professional_role', 2);
        $this->addFieldToFilter('status', 1);
        return $this;
    }
}
