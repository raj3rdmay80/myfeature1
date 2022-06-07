<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomTabs
 */


namespace Amasty\CustomTabs\Setup\Operation;

use Amasty\CustomTabs\Api\TabsRepositoryInterface;
use Amasty\CustomTabs\Model\Tabs\TabsFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class AddPredifinedTab
{
    /**
     * @var TabsFactory
     */
    private $tabsFactory;

    /**
     * @var TabsRepositoryInterface
     */
    private $repository;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $date;

    public function __construct(
        TabsFactory $tabsFactory,
        TabsRepositoryInterface $repository,
        \Magento\Framework\Stdlib\DateTime\DateTime $date
    ) {
        $this->tabsFactory = $tabsFactory;
        $this->repository = $repository;
        $this->date = $date;
    }

    /**
     * @var array
     */
    private $requiredFields = [
        'tab_title',
        'tab_name',
        'content',
        'stores'
    ];

    /**
     * @param ModuleDataSetupInterface $setup
     * @throws \Zend_Db_Exception
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function execute(ModuleDataSetupInterface $setup)
    {
        $paths = $this->getTemplates();
        foreach ($paths as $path) {
            $xmlDoc = simplexml_load_file($path);
            $tabData = $this->parseNode($xmlDoc);
            $this->createTab($tabData);
        }
    }

    /**
     * @return array
     */
    protected function getTemplates()
    {
        $p = strrpos(__DIR__, DIRECTORY_SEPARATOR);
        $directoryPath = $p ? substr(__DIR__, 0, $p) : __DIR__;
        $directoryPath .= '/../etc/predifined/';

        //phpcs:ignore
        return glob($directoryPath . '*.xml');
    }

    /**
     * @param array $data
     */
    protected function createTab($data)
    {
        if ($this->isTemplateDataValid($data)) {
            /** @var \Amasty\CustomTabs\Model\Tabs\Tabs $model */
            $model = $this->tabsFactory->create();
            $model->addData($data);
            $model->setCreatedAt($this->date->gmtDate());
            $this->repository->save($model);
        }
    }

    /**
     * @param \SimpleXMLElement $node
     * @param string $parentKeyNode
     *
     * @return array|string
     */
    protected function parseNode($node, $parentKeyNode = '')
    {
        $data = [];
        foreach ($node as $keyNode => $childNode) {
            $data[$keyNode] = (string)$childNode;
        }

        return $data;
    }

    /**
     * @param array $data
     * @return bool
     */
    private function isTemplateDataValid($data = [])
    {
        $result = true;
        foreach ($this->requiredFields as $fieldName) {
            if (!array_key_exists($fieldName, $data)) {
                $result = false;
            }
        }

        return $result;
    }
}
