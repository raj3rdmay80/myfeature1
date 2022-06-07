<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\ViewModel;

use Amasty\JetTheme\Model\ImageProvider;
use Amasty\JetTheme\Model\SocialLink\SocialLinkProvider;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Amasty\JetTheme\Api\Data\SocialLinkInterface;

class SocialViewModel implements ArgumentInterface
{
    const SOCIAL_ICON_WIDTH = 64;
    const SOCIAL_ICON_HEIGHT = 64;

    /**
     * @var SocialLinkProvider
     */
    private $socialLinkProvider;

    /**
     * @var ImageProvider
     */
    private $imageProvider;

    public function __construct(
        SocialLinkProvider $socialLinkProvider,
        ImageProvider $imageProvider
    ) {
        $this->socialLinkProvider = $socialLinkProvider;
        $this->imageProvider = $imageProvider;
    }

    /**
     * @return SocialLinkInterface[]
     */
    public function getSocialLinks(): array
    {
        return $this->socialLinkProvider->getSocialLinksForCurrentStore();
    }

    /**
     * @param $imageName
     * @return string
     * @throws NoSuchEntityException
     */
    public function getSocialImage($imageName): string
    {
        return $this->imageProvider->getResizedUrl($imageName, self::SOCIAL_ICON_WIDTH, self::SOCIAL_ICON_HEIGHT);
    }

    /**
     * @return bool
     */
    public function isShowLinksBlock(): bool
    {
        return $this->socialLinkProvider->isShowLinksBlock();
    }
}
