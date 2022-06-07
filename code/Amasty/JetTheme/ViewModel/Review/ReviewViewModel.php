<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\ViewModel\Review;

use Magento\Catalog\Model\Product;
use Magento\Framework\Module\Manager;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Review\Model\ResourceModel\Review\CollectionFactory;
use Magento\Review\Model\Review;
use Magento\Store\Model\StoreManagerInterface;

class ReviewViewModel implements ArgumentInterface
{
    const ADVANCED_REVIEW_MODULE_NAME = 'Amasty_AdvancedReview';

    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @var CollectionFactory
     */
    private $reviewCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        Manager $moduleManager,
        CollectionFactory $reviewCollectionFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->moduleManager = $moduleManager;
        $this->reviewCollectionFactory = $reviewCollectionFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * Check if the module enabled
     *
     * @return bool
     */
    public function isAdvancedReviewModuleEnabled(): bool
    {
        return (bool)$this->moduleManager->isEnabled(self::ADVANCED_REVIEW_MODULE_NAME);
    }

    /**
     * Get Reviews Count By Product
     *
     * @param Product $product
     * @return int
     */
    public function getReviewsCount(Product $product): int
    {
        $collection = $this->reviewCollectionFactory->create()->addStoreFilter(
            $this->storeManager->getStore()->getId()
        )->addStatusFilter(
            Review::STATUS_APPROVED
        )->addEntityFilter(
            'product',
            $product->getId()
        );

        return $collection->getSize();
    }
}
