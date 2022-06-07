<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomTabs
 */


namespace Amasty\CustomTabs\Controller\Adminhtml\Tabs;

use Amasty\CustomTabs\Api\Data\TabsInterface;
use Amasty\CustomTabs\Model\Source\Type;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class MassDelete
 * @package Amasty\CustomTabs\Controller\Adminhtml\Tabs
 */
class MassDelete extends AbstractMassAction
{
    /**
     * @param TabsInterface $tab
     *
     * @throws LocalizedException
     */
    protected function itemAction(TabsInterface $tab)
    {
        if ($tab->getType() == Type::CUSTOM) {
            $this->repository->deleteById($tab->getTabId());
        } else {
            throw new LocalizedException(__('You can\'t delete default tab with ID %1.', $tab->getTabId()));
        }
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    protected function getErrorMessage()
    {
        return __('We can\'t delete item right now. Please review the log and try again.');
    }

    /**
     * @param int $collectionSize
     *
     * @return \Magento\Framework\Phrase
     */
    protected function getSuccessMessage($collectionSize = 0)
    {
        if ($collectionSize) {
            return __('A total of %1 record(s) have been deleted.', $collectionSize);
        }

        return __('No records have been deleted.');
    }
}
