<?php
/**
 * Copyright © Zigly All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Zigly\ScheduleManagement\Model;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Working Mode functionality model.
 *
 * @api
 *
 * @since 100.0.2
 */
class WorkingMode extends AbstractSource implements SourceInterface, OptionSourceInterface
{
    const FULL_DAY = 'full_day';

    const HALF_DAY = 'half_day';

    const PARTIAL_DAY = 'partial_day';

    /**
     * Retrieve option array.
     *
     * @return string[]
     */
    public static function getOptionArray()
    {
        return [self::FULL_DAY => __('FULL DAY'), self::HALF_DAY => __('HALF DAY'), self::PARTIAL_DAY => __('PARTIAL DAY')];
    }

    /**
     * Retrieve option array with empty value.
     *
     * @return string[]
     */
    public function getAllOptions()
    {
        $result = [];

        foreach (self::getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }

    /**
     * Retrieve option text by option value.
     *
     * @param string $optionId
     *
     * @return string
     */
    public function getOptionText($optionId)
    {
        $options = self::getOptionArray();

        return isset($options[$optionId]) ? $options[$optionId] : null;
    }
}
