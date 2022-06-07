<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_JetTheme
 */


declare(strict_types=1);

namespace Amasty\JetTheme\Controller\Ajax;

use Amasty\JetTheme\Block\AjaxCart\PopupRenderer;
use Amasty\JetTheme\Model\Product\ProductLoader;
use Magento\Catalog\Helper\Product;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;

class QuickView extends Action implements HttpPostActionInterface
{
    /**
     * @var ProductLoader
     */
    private $productLoader;

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
        ProductLoader $productLoader,
        Product $productHelper,
        PopupRenderer $popupRenderer
    ) {
        parent::__construct($context);
        $this->productLoader = $productLoader;
        $this->productHelper = $productHelper;
        $this->popupRenderer = $popupRenderer;
    }

    public function execute(): ResultInterface
    {
        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        try {
            $productId = (int)$this->getRequest()->getParam('product');
            $product = $this->productLoader->getProduct($productId);
        } catch (LocalizedException $e) {
            $resultJson->setHttpResponseCode(400);
            $resultJson->setData(['message' => $e->getMessage()]);

            return $resultJson;
        }

        $this->productHelper->initProduct($product->getEntityId(), $this);
        $resultJson->setHttpResponseCode(200);
        $resultJson->setData([
            'message' => '',
            'render_popup' => true,
            'popup_html' => $this->popupRenderer->renderPopupHtml($product)
        ]);

        return $resultJson;
    }
}
