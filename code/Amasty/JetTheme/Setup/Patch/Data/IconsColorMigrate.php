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

class IconsColorMigrate implements DataPatchInterface
{
    /**
     * @var ConfigInterface
     */
    private $resourceConfig;

    /**
     * @var string[]
     */
    private $configMap = [
        [
            'source' => 'amasty_jettheme/color_scheme/default_background_color',
            'destination' => 'amasty_jettheme/color_scheme/icons_background_plp_color'
        ],
        [
            'source' => 'amasty_jettheme/color_scheme/default_background_color',
            'destination' => 'amasty_jettheme/color_scheme/product_items_background_plp_color'
        ],
        [
            'source' => 'amasty_jettheme/color_scheme/primary_font_color',
            'destination' => 'amasty_jettheme/color_scheme/text_color_product_items_plp'
        ]
    ];

    public function __construct(ConfigInterface $resourceConfig)
    {
        $this->resourceConfig = $resourceConfig;
    }

    /**
     * @return void
     */
    public function apply(): void
    {
        foreach ($this->configMap as $configForCopy) {
            $this->copySetting($configForCopy['source'], $configForCopy['destination']);
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

    /**
     * @param string $original
     * @param string $destination
     */
    private function copySetting(string $original, string $destination): void
    {
        $connection = $this->resourceConfig->getConnection();
        $select = $connection->select()->from($this->resourceConfig->getTable('core_config_data'))
            ->where("path = '{$original}'");

        foreach ($connection->fetchAll($select) as $config) {
            $connection->insertOnDuplicate(
                $this->resourceConfig->getTable('core_config_data'),
                [
                    'scope_id' => $config['scope_id'],
                    'scope' => $config['scope'],
                    'value' => $destination === 'amasty_jettheme/color_scheme/icons_background_plp_color'
                        ? $this->darkenHex($config['value'], 3) : $config['value'],
                    'path' => $destination
                ]
            );
        }
    }

    /**
     * @param string $value
     * @param int $percent
     * @return string
     */
    private function darkenHex(string $value, int $percent): string
    {
        $value = ltrim($value, '#');
        $value = array_map('hexdec', str_split($value, 2));

        foreach ($value as &$color) {
            $adjustAmount = $color * (-$percent / 100);
            $color = str_pad(dechex($color + $adjustAmount), 2, '0', STR_PAD_LEFT);
        }

        return '#' . implode($value);
    }
}
