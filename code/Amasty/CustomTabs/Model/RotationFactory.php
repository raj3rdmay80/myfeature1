<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomTabs
 */


declare(strict_types=1);

namespace Amasty\CustomTabs\Model;

class RotationFactory
{
    // @codingStandardsIgnoreLine
    const NAME = 'Magento\TargetRule\Block\DataProviders\Rotation';

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManagerInterface;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManagerInterface
    ) {
        $this->objectManagerInterface = $objectManagerInterface;
    }

    /**
     * @return null|mixed
     */
    public function get()
    {
        $result = null;
        if (class_exists(self::NAME)) {
            $result = $this->objectManagerInterface->get(self::NAME);
        }

        return $result;
    }
}
