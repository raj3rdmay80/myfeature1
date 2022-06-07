<?php
/**
 * Copyright © Zigly All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Zigly\Species\Model\Species\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Zigly\Species\Model\ResourceModel\Species\CollectionFactory as SpeciesCollectionFactory;

/**
 * Class SpeciesOptions
 */
class SpeciesOptions implements OptionSourceInterface
{
    /**
     * @var \Zigly\Species\Model\ResourceModel\Species\CollectionFactory
     */
    protected $speciesCollectionFactory;

    /**
     * @var array
     */
    protected $options;

    /**
     * Constructor
     *
     * @param SpeciesCollectionFactory $speciesCollectionFactory
     */
    public function __construct(SpeciesCollectionFactory $speciesCollectionFactory)
    {
        $this->speciesCollectionFactory = $speciesCollectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        $collection = $this->speciesCollectionFactory->create()
            ->addFieldToFilter('status', 1);
        $options = [];
        foreach ($collection as $species) {
            $options[] = [
                'label' => $species->getName(),
                'value' => $species->getSpeciesId(),
            ];
        }
        $this->options = $options;

        return $options;

    }

}
