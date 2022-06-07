<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Block\Footer;

use Amasty\JetTheme\Model\ConfigProvider;
use Magento\Framework\View\Element\Template;

class DefaultFooterWrapper extends Template
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        Template\Context $context,
        ConfigProvider $configProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->configProvider = $configProvider;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->configProvider->isFooterCustomLayout()) {
            return '';
        }

        return $this->getChildHtml();
    }
}
