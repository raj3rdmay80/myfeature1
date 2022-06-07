<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomTabs
 */


namespace Amasty\CustomTabs\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Amasty\Base\Helper\Deploy;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var Operation\AddPredifinedTab
     */
    private $addPredifinedTab;

    /**
     * @var Deploy
     */
    private $pubDeployer;

    public function __construct(
        Operation\AddPredifinedTab $addPredifinedTab,
        Deploy $pubDeployer
    ) {
        $this->addPredifinedTab = $addPredifinedTab;
        $this->pubDeployer = $pubDeployer;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {

        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $this->addPredifinedTab->execute($setup);
            $this->pubDeployer->deployFolder( __DIR__.'/../pub');
        }

        $setup->endSetup();
    }
}
