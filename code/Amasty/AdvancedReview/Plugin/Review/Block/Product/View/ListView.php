<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Plugin\Review\Block\Product\View;

use Amasty\AdvancedReview\Helper\Config as Config;
use Amasty\AdvancedReview\Model\Toolbar\Applier as Applier;
use Magento\Review\Block\Product\View\ListView as MagentoListView;
use Magento\Review\Model\ResourceModel\Review\Collection as ReviewCollection;

class ListView
{
    /**
     * @var Applier
     */
    private $applier;

    /**
     * @var Config
     */
    private $config;

    public function __construct(Applier $applier, Config $config)
    {
        $this->applier = $applier;
        $this->config = $config;
    }

    /**
     * @param MagentoListView $subject
     *
     * @return array
     */
    public function beforeGetReviewsCollection(
        MagentoListView $subject
    ) {
        $toolbar = $subject->getLayout()->getBlock('product_review_list.toolbar');
        if ($toolbar) {
            $limit = $toolbar->getAvailableLimit();
            $settingValue = $this->config->getReviewsPerPage();
            $limit = [$settingValue => $settingValue] + $limit;
            $limit = array_unique($limit);
            $toolbar->setAvailableLimit($limit);
        }

        return [];
    }

    /**
     * @param MagentoListView $subject
     * @param ReviewCollection $reviewCollection
     *
     * @return ReviewCollection
     */
    public function afterGetReviewsCollection(
        MagentoListView $subject,
        ReviewCollection $reviewCollection
    ) {
        $this->applier->execute($reviewCollection);

        return $reviewCollection;
    }
}
