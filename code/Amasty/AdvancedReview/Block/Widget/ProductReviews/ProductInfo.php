<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Block\Widget\ProductReviews;

use Amasty\AdvancedReview\Helper\BlockHelper;
use Amasty\AdvancedReview\ViewModel\Summary\SummaryRendererInterface;
use Magento\Catalog\Block\Product\ImageBuilder;
use Magento\Framework\View\Element\Template;
use Psr\Log\LoggerInterface;

class ProductInfo extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Amasty_AdvancedReview::widget/product_reviews/components/product_info.phtml';

    /**
     * @var BlockHelper
     */
    private $blockHelper;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ImageBuilder
     */
    private $imageBuilder;

    /**
     * @var SummaryRendererInterface
     */
    private $summaryRenderer;

    public function __construct(
        Template\Context $context,
        ImageBuilder $imageBuilder,
        BlockHelper $blockHelper,
        LoggerInterface $logger,
        SummaryRendererInterface $summaryRenderer,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->blockHelper = $blockHelper;
        $this->logger = $logger;
        $this->imageBuilder = $imageBuilder;
        $this->summaryRenderer = $summaryRenderer;
    }

    /**
     * @return string
     */
    public function getProductName()
    {
        return $this->getProduct()->getName();
    }

    /**
     * @return string
     */
    public function getProductImage()
    {
        return $this->imageBuilder->setProduct($this->getProduct())
            ->setImageId('advanced_review_product_reviews_widget_image')
            ->setAttributes([])
            ->create()
            ->toHtml();
    }

    /**
     * @return string
     */
    public function getProductUrl()
    {
        return $this->getProduct()->getProductUrl();
    }

    /**
     * @param $product
     * @param $displayedCollection
     * @return string
     */
    public function getReviewsSummaryHtml($product, $displayedCollection)
    {
        try {
            $html = $this->summaryRenderer->render($displayedCollection, $product);
        } catch (\Exception $e) {
            $html = '';
            $this->logger->error($e->getMessage());
        }

        return $html;
    }

    /**
     * @return BlockHelper
     */
    public function getAdvancedHelper()
    {
        return $this->blockHelper;
    }
}
