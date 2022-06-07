<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomTabs
 */


namespace Amasty\CustomTabs\Setup;

use Amasty\CustomTabs\Model\Tabs\Loader;
use Magento\Framework\App\State;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    /**
     * @var Loader
     */
    private $loader;

    /**
     * @var State
     */
    private $appState;

    public function __construct(State $appState, Loader $loader)
    {
        $this->loader = $loader;
        $this->appState = $appState;
    }

    /**
     * @inheritdoc
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $this->appState->emulateAreaCode(
            \Magento\Framework\App\Area::AREA_ADMINHTML,
            [$this, 'upgradeCallback'],
            [$setup, $context]
        );
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgradeCallback(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $this->loader->execute();
    }
}
