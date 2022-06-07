<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Setup\Patch\Data;

use Magento\Framework\App\Config\ConfigResource\ConfigInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class MigrateSocialLinkSetting implements DataPatchInterface
{
    const OLD_SETTING = 'amasty_jettheme/social_links/show_links_block';
    const NEW_SETTING = 'amasty_jettheme/custom_footer/show_links_block';

    /**
     * @var ConfigInterface
     */
    private $resourceConfig;

    public function __construct(ConfigInterface $resourceConfig)
    {
        $this->resourceConfig = $resourceConfig;
    }

    /**
     * @return $this
     */
    public function apply(): self
    {
        $connection = $this->resourceConfig->getConnection();
        $configTable = 'core_config_data';
        $select = $connection->select()->from($this->resourceConfig->getTable($configTable))
            ->where('path = ?', self::OLD_SETTING);

        foreach ($connection->fetchAll($select) as $config) {

            $connection->insertOnDuplicate(
                $this->resourceConfig->getTable($configTable),
                [
                    'scope_id' => $config['scope_id'],
                    'scope' => $config['scope'],
                    'value' => $config['value'],
                    'path' => self::NEW_SETTING
                ]
            );
        }

        return $this;
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
