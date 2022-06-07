<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomTabs
 */


namespace Amasty\CustomTabs\Model\Tabs\DataProvider;

use Amasty\CustomTabs\Api\Data\TabsInterface;
use Amasty\CustomTabs\Controller\Adminhtml\RegistryConstants;
use Amasty\CustomTabs\Model\Tabs\Repository;
use Amasty\CustomTabs\Model\Tabs\ResourceModel\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;

/**
 * Class Form
 */
class Form extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    public function __construct(
        CollectionFactory $tabsCollectionFactory,
        Repository $repository,
        RequestInterface $request,
        DataPersistorInterface $dataPersistor,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $tabsCollectionFactory->create();
        $this->request = $request;
        $this->repository = $repository;
        $this->dataPersistor = $dataPersistor;
    }

    /**
     * @return array
     */
    public function getData()
    {
        $data = parent::getData();
        if ($data['totalRecords'] > 0) {
            if (isset($data['items'][0]['tab_id'])) {
                $tabId = (int)$data['items'][0]['tab_id'];
                $data[$tabId] = $this->repository->getById($tabId)->getData();
            }
        }

        if ($savedData = $this->dataPersistor->get(RegistryConstants::TAB_DATA)) {
            $savedTabId = isset($savedData[TabsInterface::TAB_ID]) ? $savedData[TabsInterface::TAB_ID] : null;
            if (isset($data[$savedTabId])) {
                $data[$savedTabId] = array_merge($data[$savedTabId], $savedData);
            } else {
                $data[$savedTabId] = $savedData;
            }
            $this->dataPersistor->clear(RegistryConstants::TAB_DATA);
        }

        return $data;
    }

    /**
     * @return TabsInterface|null
     */
    protected function getCurrentTab()
    {
        $tabId = (int)$this->request->getParam(TabsInterface::TAB_ID);
        $tab = null;

        if ($tabId) {
            try {
                $tab = $this->repository->getById($tabId);
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                $tab = null;
            }
        }

        return $tab;
    }

    /**
     * @return array
     */
    public function getMeta()
    {
        $meta = parent::getMeta();

        return $meta;
    }
}
