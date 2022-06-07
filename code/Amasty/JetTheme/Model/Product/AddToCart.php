<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Model\Product;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Checkout\Model\Cart;
use Magento\Framework\Locale\ResolverInterface;

class AddToCart
{
    /**
     * @var ResolverInterface
     */
    private $localeResolver;

    /**
     * @var Cart
     */
    private $cart;

    public function __construct(
        ResolverInterface $localeResolver,
        Cart $cart
    ) {
        $this->localeResolver = $localeResolver;
        $this->cart = $cart;
    }

    /**
     * @param ProductInterface $product
     * @param array $params
     */
    public function addProductToCart(ProductInterface $product, array $params): void
    {
        if (isset($params['qty'])) {
            $filter = new \Zend_Filter_LocalizedToNormalized(
                ['locale' => $this->localeResolver->getLocale()]
            );
            $params['qty'] = $filter->filter($params['qty']);
        }
        $related = $params['related_product'] ?? null;
        $this->cart->addProduct($product, $params);

        if (!empty($related)) {
            $this->cart->addProductsByIds(explode(',', $related));
        }

        $this->cart->save();
    }
}
