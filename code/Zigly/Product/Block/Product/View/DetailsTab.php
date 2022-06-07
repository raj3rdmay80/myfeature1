<?php
/**
 * @author Zigly Team
 * @copyright Copyright (c) 2021 Zigly
 * @package Zigly_Product
 */

declare(strict_types=1);

namespace Zigly\Product\Block\Product\View;

/**
 * Product details tabs block.
 *
 * Holds a group of blocks to show as details tabs.
 *
 */
class DetailsTab extends \Magento\Framework\View\Element\Template
{
    /**
     * Get sorted child block names.
     *
     * @param string $groupName
     * @param string $callback
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @return array
     * @since 103.0.1
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getGroupSortedChildNames(string $groupName, string $callback): array
    {
        $groupChildNames = $this->getGroupChildNames($groupName);
        $layout = $this->getLayout();

        $childNamesSortOrder = [];

        foreach ($groupChildNames as $childName) {
            $alias = $layout->getElementAlias($childName);
            $sortOrder = (int)$this->getChildData($alias, 'sort_order') ?? 0;

            $childNamesSortOrder[$childName] = $sortOrder;
        }

        asort($childNamesSortOrder, SORT_NUMERIC);

        return array_keys($childNamesSortOrder);
    }
}
