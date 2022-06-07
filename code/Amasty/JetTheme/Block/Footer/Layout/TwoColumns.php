<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Block\Footer\Layout;

use Amasty\JetTheme\Model\Footer\BlockManager;
use Magento\Framework\View\Element\Template;

class TwoColumns extends Template implements LayoutInterface
{
    /**
     * @var string
     */
    protected $_template = 'Amasty_JetTheme::footer/layout/two-columns.phtml';

    /**
     * @var array
     */
    private $layoutConfig = [];

    /**
     * @var BlockManager
     */
    private $blockManager;

    public function __construct(
        Template\Context $context,
        BlockManager $blockManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->blockManager = $blockManager;
    }

    /**
     * @param array $layoutConfig
     * @return LayoutInterface
     */
    public function setLayoutConfig(array $layoutConfig): LayoutInterface
    {
        $this->layoutConfig = $layoutConfig;

        return $this;
    }

    /**
     * @return string
     */
    public function getFirstColumnsHtml(): string
    {
        return $this->blockManager->getBlocksHtmlByNames($this->layoutConfig[0]);
    }

    /**
     * @return string
     */
    public function getSecondColumnsHtml(): string
    {
        return $this->blockManager->getBlocksHtmlByNames($this->layoutConfig[1]);
    }
}
