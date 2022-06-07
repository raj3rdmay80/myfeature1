<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


declare(strict_types=1);

namespace Amasty\AdvancedReview\ViewModel\Reviews\Product\View\ListView;

use Amasty\AdvancedReview\Helper\BlockHelper;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class WriteReviewButton implements ArgumentInterface
{
    /**
     * @var BlockHelper
     */
    private $blockHelper;

    public function __construct(
        BlockHelper $blockHelper
    ) {
        $this->blockHelper = $blockHelper;
    }

    public function isCanRender(): bool
    {
        return $this->blockHelper->isAllowGuest();
    }
    
    public function getButtonUrl(): string
    {
        return '#review-form';
    }
}
