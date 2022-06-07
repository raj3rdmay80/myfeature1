<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Block\AjaxCart;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Block\Product\ImageBuilder;
use Magento\Catalog\Block\Product\View;
use Magento\Framework\Url\Helper\Data;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

class PopupRenderer
{
    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var Data
     */
    private $urlHelper;

    /**
     * @var ImageBuilder
     */
    private $imageBuilder;

    public function __construct(
        PageFactory $resultPageFactory,
        Data $urlHelper,
        ImageBuilder $imageBuilder
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->urlHelper = $urlHelper;
        $this->imageBuilder = $imageBuilder;
    }

    /**
     * @param ProductInterface $product
     * @return string
     */
    public function renderPopupHtml(ProductInterface $product): string
    {
        $page = $this->resultPageFactory->create(false, ['isIsolated' => false]);
        $page->addHandle('catalog_product_view');

        $type = $product->getTypeId();
        $page->addHandle('catalog_product_view_type_' . $type);

        $optionsHtml = $this->generateOptionsHtml($product, $page);
        $block = $page->getLayout()->createBlock(
            Popup::class,
            'amasty.jet_theme.cart.minipage',
            [
                'data' => [
                        'product' => $product,
                        'optionsHtml' => $optionsHtml,
                        'imageBuilder' => $this->imageBuilder
                    ]
            ]
        );

        return $block->toHtml();
    }

    /**
     * Generate html for product options
     * @param ProductInterface $product
     * @param Page $page
     *
     * @return string
     */
    private function generateOptionsHtml(ProductInterface $product, Page $page): string
    {
        $block = $page->getLayout()->getBlock('product.info');
        if (!$block) {
            $block = $page->getLayout()->createBlock(
                View::class,
                'product.info',
                ['data' => []]
            );
        }

        $block->setProduct($product);
        $html = $block->toHtml();

        $html = str_replace(
            '"spConfig',
            '"priceHolderSelector": ".price-box[data-product-id=' . $product->getId() . ']", "spConfig',
            $html
        );

        $contentClass = 'product-options-bottom';

        $html = '<div class="' . $contentClass . '" >' . $html . '</div>';
        $html = $this->replaceHtmlElements($product, $html);

        return $html;
    }

    /**
     * @param ProductInterface $product
     * @param string $html
     * @return string
     */
    private function replaceHtmlElements(ProductInterface $product, string $html): string
    {
        /* replace uenc for correct redirect*/
        $currentUenc = $this->urlHelper->getEncodedUrl();
        $refererUrl = $product->getProductUrl();
        $newUenc = $this->urlHelper->getEncodedUrl($refererUrl);

        $html = str_replace($currentUenc, $newUenc, $html);
        $html = str_replace('"swatch-opt"', '"swatch-opt swatch-opt-' . $product->getId() . '"', $html);
        $html = str_replace('spConfig": {"attributes', 'spConfig": {"containerId":"#confirmBox", "attributes', $html);
        $html = str_replace('[data-role=swatch-options]', '#confirmBox [data-role=swatch-options]', $html);

        return $html;
    }
}
