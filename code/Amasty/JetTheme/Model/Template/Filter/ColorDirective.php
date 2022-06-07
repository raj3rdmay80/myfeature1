<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


namespace Amasty\JetTheme\Model\Template\Filter;

use Amasty\JetTheme\Model\ConfigProvider;
use Magento\Framework\Filter\DirectiveProcessorInterface;
use Magento\Framework\Filter\Template;

class ColorDirective implements DirectiveProcessorInterface
{
    const PATTERN = '/{{color \(([a-z\_]{0,50})\)}}/si';

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(ConfigProvider $configProvider)
    {
        $this->configProvider = $configProvider;
    }

    /**
     * @param array $construction
     * @param Template $filter
     * @param array $templateVariables
     * @return string
     */
    public function process(array $construction, Template $filter, array $templateVariables): string
    {
        $value = $this->configProvider->getRgbSetting($construction[1]);

        return $value ?? '#000000';
    }

    /**
     * @return string
     */
    public function getRegularExpression(): string
    {
        return self::PATTERN;
    }
}
