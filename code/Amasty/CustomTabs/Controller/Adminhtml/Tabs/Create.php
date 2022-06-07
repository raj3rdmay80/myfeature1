<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomTabs
 */


namespace Amasty\CustomTabs\Controller\Adminhtml\Tabs;

use Amasty\CustomTabs\Controller\Adminhtml\Tabs;

/**
 * Class Index
 */
class Create extends Tabs
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
