<?php
/**
 * Customer Grid Collection
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Zigly\VetConsulting\Model\ResourceModel\Vet;

class Collection extends \Zigly\Groomer\Model\ResourceModel\Groomer\Collection
{
    /**
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addFieldToFilter('professional_role', 3);
        $this->addFieldToFilter('vet_service_center', 2);
        $this->addFieldToFilter('status', 1);
        return $this;
    }
}
