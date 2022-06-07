<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Setup;

use Amasty\Base\Helper\Deploy;
use Amasty\JetTheme\Setup\Operation\CreateCmsBlock;
use Amasty\JetTheme\Setup\Operation\CreateCmsPage;
use Amasty\JetTheme\Setup\Operation\ProcessLocalXml;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    /**
     * @var Deploy
     */
    private $pubDeployer;

    /**
     * @var ProcessLocalXml
     */
    private $processLocalXml;

    /**
     * @var CreateCmsBlock
     */
    private $createCmsBlock;

    /**
     * @var CreateCmsPage
     */
    private $createCmsPage;

    /**
     * @var AssetManager
     */
    private $assetManager;

    public function __construct(
        Deploy $pubDeployer,
        ProcessLocalXml $processLocalXml,
        CreateCmsBlock $createCmsBlock,
        CreateCmsPage $createCmsPage,
        AssetManager $assetManager
    ) {
        $this->pubDeployer = $pubDeployer;
        $this->processLocalXml = $processLocalXml;
        $this->createCmsBlock = $createCmsBlock;
        $this->createCmsPage = $createCmsPage;
        $this->assetManager = $assetManager;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context): void
    {
        $setup->startSetup();

        $this->pubDeployer->deployFolder(__DIR__ . '/../pub');
        $this->assetManager->synchronizeThemeMedia();

        $this->createCmsBlock->execute();
        $this->createCmsPage->execute($setup);

        $setup->endSetup();
    }
}
