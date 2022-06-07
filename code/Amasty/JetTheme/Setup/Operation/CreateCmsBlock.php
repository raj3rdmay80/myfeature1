<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Setup\Operation;

use Amasty\JetTheme\Api\CmsBlockManagementInterface;
use Amasty\JetTheme\Model\DirReader;
use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Cms\Api\Data\BlockInterface;
use Magento\Cms\Model\BlockFactory;
use Magento\Cms\Model\ResourceModel\Block;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\Driver\File as FileReader;

class CreateCmsBlock
{
    const SAMPLE_DIR = 'Blocks';

    /**
     * @var BlockFactory
     */
    private $blockFactory;

    /**
     * @var DirReader
     */
    private $moduleDirReader;

    /**
     * @var FileReader
     */
    private $fileReader;

    /**
     * @var BlockRepositoryInterface
     */
    private $blockRepository;

    /**
     * @var CmsBlockManagementInterface
     */
    private $cmsBlockManagement;

    /**
     * @var Block
     */
    private $blockResource;

    public function __construct(
        BlockFactory $blockFactory,
        DirReader $moduleDirReader,
        FileReader $fileReader,
        BlockRepositoryInterface $blockRepository,
        CmsBlockManagementInterface $cmsBlockManagement,
        Block $blockResource
    ) {
        $this->blockFactory = $blockFactory;
        $this->moduleDirReader = $moduleDirReader;
        $this->fileReader = $fileReader;
        $this->blockRepository = $blockRepository;
        $this->cmsBlockManagement = $cmsBlockManagement;
        $this->blockResource = $blockResource;
    }

    /**
     * Create cms blocks from fixtures
     *
     * @return void
     */
    public function execute(): void
    {
        $blocks = $this->cmsBlockManagement->getAllBlocks();

        foreach ($blocks as $block) {
            $this->createCmsBlock($block);
        }
    }

    /**
     * @param array $block
     * @return void
     */
    public function createCmsBlock($block): void
    {
        $blockEntity = $this->blockFactory->create();
        $sampleFolderPath = $this->moduleDirReader->getSampleModuleDir(self::SAMPLE_DIR);
        $content = $this->fileReader->fileGetContents($sampleFolderPath . $block['file']);

        $this->checkExistBlockIdentifier($block[BlockInterface::IDENTIFIER]);

        $blockData = [
            BlockInterface::TITLE => $block[BlockInterface::TITLE],
            BlockInterface::IDENTIFIER => $block[BlockInterface::IDENTIFIER],
            BlockInterface::IS_ACTIVE => $block[BlockInterface::IS_ACTIVE],
            BlockInterface::CONTENT => $content
        ];

        $blockEntity->addData($blockData);
        $this->blockRepository->save($blockEntity);
    }

    /**
     * @param string $identifier
     * @return void
     */
    private function checkExistBlockIdentifier($identifier): void
    {
        $block = $this->blockFactory->create();
        $this->blockResource->load($block, $identifier);

        if ($block->getId()) {
            $rand = random_int(1, 999);
            $block->setIdentifier($block->getIdentifier() . '-old-' . $rand);
            $this->blockRepository->save($block);
        }
    }
}
