<?php
/**
 * Copyright © Zigly All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Zigly\Species\Api\Data;

interface SpeciesSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Species list.
     * @return \Zigly\Species\Api\Data\SpeciesInterface[]
     */
    public function getItems();

    /**
     * Set name list.
     * @param \Zigly\Species\Api\Data\SpeciesInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

