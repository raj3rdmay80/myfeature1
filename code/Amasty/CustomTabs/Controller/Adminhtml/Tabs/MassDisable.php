<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomTabs
 */


namespace Amasty\CustomTabs\Controller\Adminhtml\Tabs;

use Amasty\CustomTabs\Api\Data\TabsInterface;

/**
 * Class MassDelete
 */
class MassDisable extends AbstractMassAction
{
    /**
     * {@inheritdoc}
     */
    protected function itemAction(TabsInterface $tab)
    {
        $tab->setStatus(0);
        $this->repository->save($tab);
    }
}
