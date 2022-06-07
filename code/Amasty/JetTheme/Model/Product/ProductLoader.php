<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Model\Product;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

class ProductLoader
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    public function __construct(
        StoreManagerInterface $storeManager,
        ProductRepositoryInterface $productRepository
    ) {

        $this->storeManager = $storeManager;
        $this->productRepository = $productRepository;
    }

    /**
     * @param int $productId
     * @return ProductInterface
     * @throws LocalizedException
     */
    public function getProduct(int $productId): ProductInterface
    {
        $storeId = $this->storeManager->getStore()->getId();
        try {
            $product = $this->productRepository->getById($productId, false, $storeId);
        } catch (NoSuchEntityException $e) {
            throw new LocalizedException(__('Product does not exist'));
        }

        return $product;
    }
}
