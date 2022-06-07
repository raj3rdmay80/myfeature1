<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomTabs
 */


namespace Amasty\CustomTabs\Controller\Adminhtml\Tabs;

use Amasty\CustomTabs\Controller\Adminhtml\Tabs;
use Amasty\CustomTabs\Model\ConfigProvider;
use Amasty\CustomTabs\Model\Tabs\Repository;
use Amasty\CustomTabs\Model\Source\Type;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Amasty\CustomTabs\Api\Data\TabsInterface;

/**
 * Class Edit
 */
class Edit extends Tabs
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        Repository $repository,
        ConfigProvider $configProvider,
        Action\Context $context
    ) {
        parent::__construct($context);
        $this->repository = $repository;
        $this->configProvider = $configProvider;
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        if ($tabId = (int) $this->getRequest()->getParam(TabsInterface::TAB_ID)) {
            try {
                $tab = $this->repository->getById($tabId);
                $this->checkDefaultTab($tab);
                /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
                $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
                $resultPage->getConfig()->getTitle()->prepend(__('Edit Tab "%1"', $tab->getTabName()));
            } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
                $this->messageManager->addErrorMessage(__('This tab no longer exists.'));

                return $this->_redirect('*/*/index');
            }
        } else {
            /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
            $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
            $resultPage->getConfig()->getTitle()->prepend(__('New Tab'));
        }

        return $resultPage;
    }

    /**
     * @param TabsInterface $tab
     */
    private function checkDefaultTab($tab)
    {
        if (!$this->configProvider->isChangeDefaultTabsAllowed() && $tab->getType() != Type::CUSTOM) {
            $this->messageManager->addComplexNoticeMessage(
                'addTabNonEditableMessage',
                ['config_url' => $this->getUrl('adminhtml/system_config/edit/section/amcustomtabs')]
            );
        }
    }
}
