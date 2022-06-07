<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbyLite
 */


declare(strict_types=1);

namespace Amasty\ShopbyLite\Setup\Patch\Data;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\Module\Status as ModuleStatus;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class DisalbeIfConflict implements DataPatchInterface
{
    /**
     * @var ModuleManager
     */
    private $moduleManager;

    /**
     * @var ModuleStatus
     */
    private $moduleStatus;

    public function __construct(
        ModuleManager $moduleManager,
        ModuleStatus $moduleStatus
    ) {
        $this->moduleManager = $moduleManager;
        $this->moduleStatus = $moduleStatus;
    }

    /**
     * @return void
     */
    public function apply(): void
    {
        if ($this->moduleManager->isEnabled('Amasty_Shopby')) {
            try {
                $this->moduleStatus->setIsEnabled(false, ['Amasty_ShopbyLite']);
            } catch (\Exception $e) {
                throw new LocalizedException(
                    __('Please disable Amasty_ShopbyLite module manually, because it conflicts with Amasty_Shopby')
                );
            }
        }
    }

    /**
     * @return array
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * @return string[]
     */
    public static function getDependencies(): array
    {
        return [];
    }
}
