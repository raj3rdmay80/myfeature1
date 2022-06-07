<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Block\AjaxCart;

use Magento\Catalog\Block\Product\ImageBuilder;
use Magento\Catalog\Block\Product\ReviewRendererInterface as ReviewRendererInterface;
use Magento\Catalog\Block\Product\View;
use Magento\Catalog\Model\Product;
use Magento\Review\Block\Product\ReviewRenderer;

class Popup extends View
{
    public function _construct()
    {
        $this->setTemplate('Amasty_JetTheme::product/add_to_cart_popup.phtml');
        parent::_construct();
    }

    /**
     * @return Product|null
     */
    public function getProduct(): ?Product
    {
        return $this->getData('product');
    }

    /**
     * @return ImageBuilder|null
     */
    public function getImageBuilder(): ?ImageBuilder
    {
        return $this->getData('imageBuilder');
    }

    /**
     * @return string
     */
    public function renderPriceHtml(): string
    {
        $html = '';
        $block = $this->_layout->getBlock('product.price.final');

        if ($block) {
            $html = $block->toHtml();
        }

        return $html;
    }

    /**
     * @return string|null
     */
    public function getOptions(): ?string
    {
        return $this->getData('optionsHtml');
    }

    /**
     * @return string
     */
    public function getRatingSummary($product): string
    {
        $block = $this->getLayout()->createBlock(
            ReviewRenderer::class,
            'amasty.productreview',
            [
                'data' => [
                    'product' => $product
                ]
            ]
        );

        return $block->getReviewsSummaryHtml($product, ReviewRendererInterface::SHORT_VIEW);
    }

    /**
     * @param Product $product
     * @param string $imageId
     * @param array $attributes
     *
     * @return string
     */
    public function getImageBlock($product, $imageId, $attributes = []): string
    {
        $block = $this->getImageBuilder()->setProduct($product)
            ->setImageId($imageId)
            ->setAttributes($attributes)
            ->create();

        return $block->toHtml();
    }
}
