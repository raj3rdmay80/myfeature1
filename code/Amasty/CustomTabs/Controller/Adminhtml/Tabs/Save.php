<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomTabs
 */


namespace Amasty\CustomTabs\Controller\Adminhtml\Tabs;

use Amasty\CustomTabs\Api\Data\TabsInterface;
use Amasty\CustomTabs\Controller\Adminhtml\Tabs;
use Amasty\CustomTabs\Controller\Adminhtml\RegistryConstants;
use Magento\Backend\App\Action\Context;
use Amasty\CustomTabs\Model\Tabs\TabsFactory;
use Amasty\CustomTabs\Api\TabsRepositoryInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;

class Save extends Tabs
{
    /**
     * @var TabsRepositoryInterface
     */
    private $repository;

    /**
     * @var TabsFactory
     */
    private $tabsFactory;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    public function __construct(
        Context $context,
        TabsFactory $tabsFactory,
        TabsRepositoryInterface $repository,
        DataPersistorInterface $dataPersistor
    ) {
        parent::__construct($context);
        $this->tabsFactory = $tabsFactory;
        $this->repository = $repository;
        $this->dataPersistor = $dataPersistor;
    }

    public function execute()
    {
        if ($data = $this->getRequest()->getPostValue()) {
            try {
                $model = $this->getTabModel();
                $this->filterData($data);
                $model->addData($data);
                $this->repository->save($model);

                $this->messageManager->addSuccessMessage(__('Tab has been saved.'));

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect(
                        '*/*/edit',
                        [TabsInterface::TAB_ID => $model->getTabId(), '_current' => true]
                    );
                    return;
                }
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->dataPersistor->set(RegistryConstants::TAB_DATA, $data);
                if ($tabId = (int)$this->getRequest()->getParam(TabsInterface::TAB_ID)) {
                    $this->_redirect('*/*/edit', [TabsInterface::TAB_ID => $tabId]);
                } else {
                    $this->_redirect('*/*/create');
                }
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * @return TabsInterface|\Amasty\CustomTabs\Model\Tabs\Tabs
     * @throws LocalizedException
     */
    protected function getTabModel()
    {
        /** @var \Amasty\CustomTabs\Model\Tabs\Tabs $model */
        $model = $this->tabsFactory->create();

        if ($tabId = (int)$this->getRequest()->getParam(TabsInterface::TAB_ID)) {
            $model = $this->repository->getById($tabId);
            if ($tabId != $model->getTabId()) {
                throw new LocalizedException(__('The wrong item is specified.'));
            }
        }

        return $model;
    }

    /**
     * @param array $data
     */
    private function filterData(&$data)
    {
        unset($data['tab_id']);
        if (isset($data['customer_groups'])) {
            $data['customer_groups'] = implode(',', $data['customer_groups']);
        }

        if (!isset($data['module_name']) || !$data['module_name']) {
            $data['module_name'] = 'Amasty_ProductTabs';
        }

        if (isset($data['rule']['conditions'])) {
            $data['conditions'] = $data['rule']['conditions'];
            unset($data['rule']);
        }

        if (isset($data['sort_order']) && empty($data['sort_order'])) {
            $data['sort_order'] = null;
        }
    }
}
