<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomTabs
 */


namespace Amasty\CustomTabs\Observer\Backend;

use Amasty\CustomTabs\Model\Tabs\Indexer\ProductProcessor;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class CatalogProductSaveAfterObserver implements ObserverInterface
{
    /**
     * @var ProductProcessor
     */
    private $productProcessor;

    public function __construct(
        ProductProcessor $productProcessor
    ) {
        $this->productProcessor = $productProcessor;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();

        if ($product) {
            $this->productProcessor->reindexRow($product->getId());
        }
    }
}
