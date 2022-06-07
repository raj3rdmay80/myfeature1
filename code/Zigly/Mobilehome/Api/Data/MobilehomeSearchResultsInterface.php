<?php
declare(strict_types=1);

namespace Zigly\Mobilehome\Api\Data;

interface MobilehomeSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get mobilehome list.
     * @return \Zigly\Mobilehome\Api\Data\MobilehomeInterface[]
     */
    public function getItems();

    /**
     * Set name list.
     * @param \Zigly\Mobilehome\Api\Data\MobilehomeInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

