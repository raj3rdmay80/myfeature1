<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomTabs
 */


namespace Amasty\CustomTabs\Model\Layout;

/**
 * Class GeneratorPool
 */
class GeneratorPool extends \Magento\Framework\View\Layout\GeneratorPool
{
    /**
     * @inheritdoc
     */
    protected function addGenerators(array $generators)
    {
        $this->generators = [];
    }
}
