<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Controller\Ajax;

use Amasty\JetTheme\Model\Product\ProductLoader;
use Amasty\JetTheme\Model\Validator\AddToRequestValidator;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Wishlist\Controller\WishlistProviderInterface;
use Magento\Wishlist\Helper\Data;
use Magento\Wishlist\Model\Wishlist;

class AddToWishlist extends Action implements HttpPostActionInterface
{
    /**
     * @var AddToRequestValidator
     */
    private $addToRequestValidator;

    /**
     * @var WishlistProviderInterface
     */
    private $wishlistProvider;

    /**
     * @var Data
     */
    private $wishlistHelper;

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var ProductLoader
     */
    private $productLoader;

    public function __construct(
        Context $context,
        AddToRequestValidator $addToRequestValidator,
        WishlistProviderInterface $wishlistProvider,
        Data $wishlistHelper,
        Session $customerSession,
        ProductLoader $productLoader
    ) {
        parent::__construct($context);
        $this->addToRequestValidator = $addToRequestValidator;
        $this->wishlistProvider = $wishlistProvider;
        $this->wishlistHelper = $wishlistHelper;
        $this->customerSession = $customerSession;
        $this->productLoader = $productLoader;
    }

    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $isModal = $this->getRequest()->getParam('is_modal_shown');

        if (!$this->customerSession->isLoggedIn()) {
            $resultJson->setHttpResponseCode(401);
            $message = __('You must login or register to add items to your wishlist.');
            $resultJson->setData(['message' => $message]);
            if (!$isModal) {
                $this->messageManager->addErrorMessage($message);
            }

            return $resultJson;
        }

        try {
            $this->addToRequestValidator->validateRequest($this->getRequest());
            $productId = (int)$this->getRequest()->getParam('product');
            $product = $this->validateAndGetProduct($productId);
            $wishlist = $this->getWishlist();
        } catch (LocalizedException $e) {
            $resultJson->setHttpResponseCode(400);
            $resultJson->setData(['message' => $e->getMessage()]);
            if (!$isModal) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }

            return $resultJson;
        }

        $requestParams = $this->getRequest()->getParams();
        try {
            $buyRequest = new DataObject($requestParams);
            $result = $wishlist->addNewItem($product, $buyRequest);
            if (is_string($result)) {
                $resultJson->setHttpResponseCode(400);
                $resultJson->setData(['message' => __($result)]);
                if (!$isModal) {
                    $this->messageManager->addErrorMessage($result);
                }

                return $resultJson;
            }
            if ($wishlist->isObjectNew()) {
                $wishlist->save();
            }

            $this->_eventManager->dispatch(
                'wishlist_add_product',
                ['wishlist' => $wishlist, 'product' => $product, 'item' => $result]
            );

            $this->wishlistHelper->calculate();
        } catch (LocalizedException $e) {
            $resultJson->setHttpResponseCode(400);
            $message = __('We can\'t add the item to Wish List right now: %1.', $e->getMessage());
            $resultJson->setData([
                'message' => $message
            ]);
            if (!$isModal) {
                $this->messageManager->addErrorMessage($message);
            }

            return $resultJson;
        } catch (\Exception $e) {
            $resultJson->setHttpResponseCode(400);
            $message = __('We can\'t add the item to Wish List right now.');
            $resultJson->setData([
                'message' => $message
            ]);
            if (!$isModal) {
                $this->messageManager->addErrorMessage($message);
            }

            return $resultJson;
        }

        $resultJson->setHttpResponseCode(200);
        $this->messageManager->addComplexSuccessMessage(
            'amAjaxSuccessAddToWishlist',
            [
                'product_name' => $product->getName(),
                'wishlist_url' => $this->getWishListUrl(),
            ]
        );
        $result->setData(['success' => true]);

        return $resultJson;
    }

    /**
     * Returns wishlist url
     *
     * @return string
     */
    private function getWishListUrl(): string
    {
        return $this->_url->getUrl('wishlist', ['_secure' => true]);
    }

    /**
     * @param int $productId
     * @return ProductInterface
     * @throws LocalizedException
     */
    private function validateAndGetProduct(int $productId): ProductInterface
    {
        $product = $this->productLoader->getProduct($productId);

        if (!$product->isVisibleInCatalog()) {
            throw new LocalizedException(__('Product is not available for wishlist'));
        }

        return $product;
    }

    /**
     * @return Wishlist
     * @throws LocalizedException
     */
    private function getWishlist(): Wishlist
    {
        $wishlist = $this->wishlistProvider->getWishlist();
        if (!$wishlist) {
            throw new LocalizedException(__('Wishlist not found'));
        }

        return $wishlist;
    }
}
