<?php
/**
 * Copyright © Zigly All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Zigly\Species\Api\Data;

interface BreedSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Breed list.
     * @return \Zigly\Species\Api\Data\BreedInterface[]
     */
    public function getItems();

    /**
     * Set species_id list.
     * @param \Zigly\Species\Api\Data\BreedInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

