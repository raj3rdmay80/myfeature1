<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomTabs
 */


namespace Amasty\CustomTabs\Model\Source;

use Magento\Customer\Ui\Component\Listing\Column\Group\Options;

class CustomerGroup extends Options
{
    const ALL = -1;

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        $options = parent::toOptionArray();
        $options[] = [
            'value' => self::ALL,
            'label' => __('All')
        ];

        return $options;
    }
}
