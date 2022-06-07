<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Setup\Operation;

use Amasty\JetTheme\Api\CmsPageManagementInterface;
use Amasty\JetTheme\Model\DirReader;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Framework\Filesystem\Driver\File as FileReader;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class CreateCmsPage
{
    const SAMPLE_DIR = 'Pages';

    /**
     * @var DirReader
     */
    private $moduleDirReader;

    /**
     * @var FileReader
     */
    private $fileReader;

    /**
     * @var CmsPageManagementInterface
     */
    private $cmsPageManagement;

    public function __construct(
        DirReader $moduleDirReader,
        FileReader $fileReader,
        CmsPageManagementInterface $cmsPageManagement
    ) {
        $this->moduleDirReader = $moduleDirReader;
        $this->fileReader = $fileReader;
        $this->cmsPageManagement = $cmsPageManagement;
    }

    /**
     * Create cms pages
     * @param ModuleDataSetupInterface $setu
     * @return void
     */
    public function execute(ModuleDataSetupInterface $setup): void
    {
        $pagesData = $this->cmsPageManagement->getAllPages();
        $sampleFolderPath = $this->moduleDirReader->getSampleModuleDir(self::SAMPLE_DIR);

        foreach ($pagesData as $pageData) {
            $pageData[PageInterface::CONTENT] = $this->fileReader->fileGetContents(
                $sampleFolderPath . $pageData['file']
            );
            unset($pageData['file']);
            $this->createCmsPage($setup, $pageData);
        }
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param array $pageData
     * @return void
     */
    private function createCmsPage(ModuleDataSetupInterface $setup, array $pageData): void
    {
        $connection = $setup->getConnection();
        $pageTable = $setup->getTable('cms_page');
        $pageStoreTable = $setup->getTable('cms_page_store');
        $sequencePageTable = $setup->getTable('sequence_cms_page');
        $isStaging = $connection->isTableExists($sequencePageTable);
        $existingPageData = $connection->fetchRow(
            $connection
                ->select()
                ->from($pageTable)
                ->where(PageInterface::IDENTIFIER . ' = ?', $pageData[PageInterface::IDENTIFIER])
        );

        $pageStoreLinkColumn = 'page_id';
        if ($isStaging) {
            $pageStoreLinkColumn = 'row_id';
        }

        if ($existingPageData) {
            $pageId = $existingPageData[PageInterface::PAGE_ID];
            unset($existingPageData[PageInterface::PAGE_ID]);
            if ($isStaging) {
                unset($existingPageData['row_id']);
                $connection->insert($sequencePageTable, ['sequence_value' => new \Zend_Db_Expr('NULL')]);
                $existingPageData[PageInterface::PAGE_ID] = $connection->lastInsertId();
            }
            $rand = random_int(1, 999);
            $existingPageData[PageInterface::IDENTIFIER] .= '-old-' . $rand;

            $connection->insert($pageTable, $existingPageData);
            $newPageId = $connection->lastInsertId();
            $connection->query(
                $connection->insertFromSelect(
                    $connection->select()
                        ->from($pageStoreTable, [new \Zend_Db_Expr($newPageId), 'store_id'])
                        ->where($pageStoreLinkColumn . ' = ?', $pageId),
                    $pageStoreTable,
                    [
                        $pageStoreLinkColumn,
                        'store_id'
                    ]
                )
            );

            $pageDataForUpdate = [
                PageInterface::CONTENT => $pageData[PageInterface::CONTENT],
                PageInterface::PAGE_LAYOUT => $pageData[PageInterface::PAGE_LAYOUT],
            ];

            if (array_key_exists('layout_update_selected', $pageData)) {
                $pageDataForUpdate['layout_update_selected'] = new \Zend_Db_Expr('NULL');
            }
            $connection->update($pageTable, $pageDataForUpdate, [
                PageInterface::PAGE_ID . ' = ?' => $pageId
            ]);
        } else {
            $connection->insert($pageTable, $pageData);
            $pageId = $connection->lastInsertId();
            $connection->insert(
                $pageStoreTable,
                [
                    $pageStoreLinkColumn => $pageId,
                    'store_id' => 0
                ]
            );
        }
    }
}
