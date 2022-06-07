<?php
/**
 * Copyright © Zigly All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Zigly\ScheduleManagementApi\Model\GroomingHub\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Zigly\Cityscreen\Model\ResourceModel\Cityscreen\CollectionFactory as PincodeCollectionFactory;

/**
 * Class Pincode
 */
class Pincode implements OptionSourceInterface
{
    /**
     * @var Zigly\ScheduleManagementApi\Model\ResourceModel\GroomingHub\CollectionFactory 
     */
    protected $PincodeCollectionFactory;

    /**
     * @var array
     */
    protected $options;

    /**
     * Constructor
     *
     * @param PincodeCollectionFactory $PincodeCollectionFactory
     */
    public function __construct(PincodeCollectionFactory $PincodeCollectionFactory)
    {
        $this->PincodeCollectionFactory = $PincodeCollectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        $collection = $this->PincodeCollectionFactory->create()
            ->addFieldToFilter('is_active', 1);
        $options = [];
        foreach ($collection as $pincode) {
            $options[] = [
                'label' => $pincode->getCity()."--".$pincode->getPincode(),
                'value' => $pincode->getPincode(),
            ];
        }
        $this->options = $options;

        return $options;

    }

}
