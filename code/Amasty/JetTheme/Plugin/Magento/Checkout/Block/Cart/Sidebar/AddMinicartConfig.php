<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Plugin\Magento\Checkout\Block\Cart\Sidebar;

use Amasty\JetTheme\Model\ConfigProvider;
use Magento\Checkout\Block\Cart\Sidebar;
use Magento\Framework\Module\Manager;
use Magento\Framework\Serialize\SerializerInterface;

class AddMinicartConfig
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var Manager
     */
    private $moduleManager;

    public function __construct(
        ConfigProvider $configProvider,
        SerializerInterface $serializer,
        Manager $moduleManager
    ) {
        $this->configProvider = $configProvider;
        $this->serializer = $serializer;
        $this->moduleManager = $moduleManager;
    }

    /**
     * @param Sidebar $subject
     * @param $serializedConfig
     */
    public function afterGetJsLayout(Sidebar $subject, $serializedConfig): string
    {
        $config = $this->serializer->unserialize($serializedConfig);
        if (!empty($config['components']['minicart_content']['config'])) {
            $config['components']['minicart_content']['config']['is_open_minicart'] =
                $this->configProvider->isOpenMinicart() && !$this->moduleManager->isEnabled('Amasty_Cart');
        }

        return $this->serializer->serialize($config);
    }
}
