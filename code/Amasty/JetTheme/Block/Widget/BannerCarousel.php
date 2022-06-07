<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class BannerCarousel extends Template implements BlockInterface
{
    const SLIDE_IMAGE_PARAM = 'slide_image_';
    const SLIDE_IMAGE_ALT_PARAM = 'slide_image_alt_';
    const SLIDE_IMAGE_LINK_PARAM = 'slide_link_';

    /**
     * @var string
     */
    protected $_template = "widget/homepage-carousel.phtml";

    /**
     * @return array Array of images: [['url' => 'Some image url', 'alt' => 'Some image alt']]
     */
    public function getImages(): array
    {
        $images = [];
        for ($i = 1; $i <= 3; $i++) {
            $images[] = [
                'url' => $this->getData(self::SLIDE_IMAGE_PARAM . $i),
                'alt' => $this->getData(self::SLIDE_IMAGE_ALT_PARAM . $i),
                'link' => $this->getData(self::SLIDE_IMAGE_LINK_PARAM . $i),
            ];
        }

        return $images;
    }
}
