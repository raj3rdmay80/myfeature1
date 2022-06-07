<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Model\OptionSource;

use Amasty\JetTheme\Model\SocialLink\SvgProvider;
use Magento\Framework\Data\OptionSourceInterface;

class SocialImages implements OptionSourceInterface
{
    /**
     * @var SvgProvider
     */
    private $svgProvider;

    public function __construct(SvgProvider $svgProvider)
    {
        $this->svgProvider = $svgProvider;
    }

    /**
     * @var array
     */
    private $options = [];

    /**
     * @return array|null
     */
    public function toOptionArray(): array
    {
        if (!$this->options) {
            foreach ($this->svgProvider->getAllSvg() as $key => $svg) {
                $this->options[] = [
                    'value' => $key,
                    'label' => $svg
                ];
            }
        }

        return $this->options;
    }
}
