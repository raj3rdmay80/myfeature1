<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Controller\Ajax;

use Amasty\JetTheme\Block\AjaxCart\PopupRenderer;
use Amasty\JetTheme\Model\Product\AddToCart as AddToCartModel;
use Amasty\JetTheme\Model\Product\ProductLoader;
use Amasty\JetTheme\Model\Validator\AddToCartRequiredOptionsValidator;
use Amasty\JetTheme\Model\Validator\AddToRequestValidator;
use Magento\Catalog\Helper\Product;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class AddToCart extends Action implements HttpPostActionInterface
{
    /**
     * @var AddToRequestValidator
     */
    private $addToRequestValidator;

    /**
     * @var ProductLoader
     */
    private $productLoader;

    /**
     * @var AddToCartRequiredOptionsValidator
     */
    private $addToCartRequiredOptionsValidator;

    /**
     * @var AddToCartModel
     */
    private $addToCart;

    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Product
     */
    private $productHelper;

    /**
     * @var PopupRenderer
     */
    private $popupRenderer;

    public function __construct(
        Context $context,
        AddToRequestValidator $addToRequestValidator,
        ProductLoader $productLoader,
        AddToCartRequiredOptionsValidator $addToCartRequiredOptionsValidator,
        AddToCartModel $addToCart,
        Escaper $escaper,
        LoggerInterface $logger,
        Product $productHelper,
        PopupRenderer $popupRenderer
    ) {
        parent::__construct($context);
        $this->addToRequestValidator = $addToRequestValidator;
        $this->productLoader = $productLoader;
        $this->addToCartRequiredOptionsValidator = $addToCartRequiredOptionsValidator;
        $this->addToCart = $addToCart;
        $this->escaper = $escaper;
        $this->logger = $logger;
        $this->productHelper = $productHelper;
        $this->popupRenderer = $popupRenderer;
    }

    /**
     * @return ResponseInterface|Json|ResultInterface
     */
    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $isModal = $this->getRequest()->getParam('is_modal_shown');
        try {
            $this->addToRequestValidator->validateRequest($this->getRequest());
            $productId = (int)$this->getRequest()->getParam('product');
            $product = $this->productLoader->getProduct($productId);
        } catch (LocalizedException $e) {
            $resultJson->setHttpResponseCode(400);
            $resultJson->setData(['message' => $e->getMessage()]);
            if (!$isModal) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }

            return $resultJson;
        }

        $params = $this->getRequest()->getParams();
        if ($this->addToCartRequiredOptionsValidator->validate($product, $params)) {
            // add to cart
            try {
                $this->addToCart->addProductToCart($product, $params);

                $this->_eventManager->dispatch(
                    'checkout_cart_add_product_complete',
                    ['product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse()]
                );

                $this->messageManager->addComplexSuccessMessage(
                    'addCartSuccessMessage',
                    [
                        'product_name' => $product->getName(),
                        'cart_url' => $this->getCartUrl(),
                    ]
                );

                $resultJson->setHttpResponseCode(200);
                $resultJson->setData(['render_popup' => false, 'success' => true]);
            } catch (\Exception $e) {
                $resultJson->setHttpResponseCode(400);
                $message = __('We can\'t add this item to your shopping cart right now.');
                $resultJson->setData(['message' => $message]);
                if (!$isModal) {
                    $this->messageManager->addErrorMessage($message);
                }

                $this->logger->critical($e);
            }
        } else {
            $this->productHelper->initProduct($product->getEntityId(), $this);

            $resultJson->setHttpResponseCode(200);
            $resultJson->setData([
                'message' => '',
                'render_popup' => true,
                'popup_html' => $this->popupRenderer->renderPopupHtml($product)
            ]);

            return $resultJson;
        }

        return $resultJson;
    }

    /**
     * Returns cart url
     *
     * @return string
     */
    private function getCartUrl(): string
    {
        return $this->_url->getUrl('checkout/cart', ['_secure' => true]);
    }
}
